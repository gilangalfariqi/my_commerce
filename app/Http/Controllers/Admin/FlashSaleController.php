<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;

class FlashSaleController extends Controller
{
    public function index(): View
    {
        $flashSales = FlashSale::withCount('items')->latest()->paginate(10);
        return view('admin.flash-sales.index', compact('flashSales'));
    }

    public function create(): View
    {
        $products = Product::active()->get();
        return view('admin.flash-sales.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'is_active' => 'nullable|boolean',
            'products' => 'required|array',
            'products.*' => 'exists:products,id',
            'discounted_prices' => 'required|array',
            'stock_limits' => 'required|array',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $flashSale = FlashSale::create([
                    'name' => $request->name,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'is_active' => $request->has('is_active'),
                ]);

                foreach ($request->products as $productId) {
                    FlashSaleItem::create([
                        'flash_sale_id' => $flashSale->id,
                        'product_id' => $productId,
                        'discounted_price' => $request->discounted_prices[$productId] ?? 0,
                        'stock_limit' => $request->stock_limits[$productId] ?? 0,
                        'stock_sold' => 0,
                        'order_limit' => 2, // Default limit per customer
                    ]);
                }
            });

            return redirect()->route('admin.flash-sales.index')->with('success', 'Flash Sale created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create Flash Sale: ' . $e->getMessage());
        }
    }

    public function edit(FlashSale $flashSale): View
    {
        $flashSale->load('items.product');
        $products = Product::active()->get();
        return view('admin.flash-sales.edit', compact('flashSale', 'products'));
    }

    public function update(Request $request, FlashSale $flashSale): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'is_active' => 'nullable|boolean',
            'products' => 'required|array',
            'products.*' => 'exists:products,id',
            'discounted_prices' => 'required|array',
            'stock_limits' => 'required|array',
        ]);

        try {
            DB::transaction(function () use ($request, $flashSale) {
                $flashSale->update([
                    'name' => $request->name,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'is_active' => $request->has('is_active'),
                ]);

                // Delete old items
                $flashSale->items()->delete();

                // Re-insert new items
                foreach ($request->products as $productId) {
                    FlashSaleItem::create([
                        'flash_sale_id' => $flashSale->id,
                        'product_id' => $productId,
                        'discounted_price' => $request->discounted_prices[$productId] ?? 0,
                        'stock_limit' => $request->stock_limits[$productId] ?? 0,
                        'stock_sold' => 0,
                        'order_limit' => 2,
                    ]);
                }
            });

            return redirect()->route('admin.flash-sales.index')->with('success', 'Flash Sale updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update Flash Sale: ' . $e->getMessage());
        }
    }

    public function destroy(FlashSale $flashSale): RedirectResponse
    {
        $flashSale->items()->delete();
        $flashSale->delete();
        return redirect()->route('admin.flash-sales.index')->with('success', 'Flash Sale deleted successfully.');
    }
}
