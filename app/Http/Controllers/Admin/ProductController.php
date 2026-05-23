<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['primaryImage', 'categories']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'weight' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'primary_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            // Variants
            'variant_names.*' => 'nullable|string|max:100',
            'variant_skus.*' => 'nullable|string|max:100',
            'variant_prices.*' => 'nullable|numeric|min:0',
            'variant_stocks.*' => 'nullable|integer|min:0',
            'variant_weights.*' => 'nullable|integer|min:0',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $product = Product::create([
                    'name' => $request->name,
                    'slug' => Str::slug($request->name) . '-' . rand(1000, 9999),
                    'sku' => $request->sku,
                    'price' => $request->price,
                    'compare_at_price' => $request->compare_at_price,
                    'cost_price' => $request->cost_price,
                    'weight' => $request->weight,
                    'stock' => $request->stock,
                    'description' => $request->description,
                    'short_description' => $request->short_description,
                    'is_active' => $request->has('is_active'),
                    'is_featured' => $request->has('is_featured'),
                ]);

                // Sync categories
                $product->categories()->sync($request->categories);

                // Upload primary image
                if ($request->hasFile('primary_image')) {
                    $path = $request->file('primary_image')->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => true,
                        'sort_order' => 0,
                    ]);
                }

                // Upload extra images
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $index => $file) {
                        $path = $file->store('products', 'public');
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $path,
                            'is_primary' => false,
                            'sort_order' => $index + 1,
                        ]);
                    }
                }

                // Save variants if any
                if ($request->filled('variant_names')) {
                    foreach ($request->variant_names as $index => $vName) {
                        if (!empty($vName)) {
                            ProductVariant::create([
                                'product_id' => $product->id,
                                'name' => $vName,
                                'sku' => $request->variant_skus[$index] ?? ($product->sku . '-' . strtoupper(Str::random(4))),
                                'price' => $request->variant_prices[$index] ?? $product->price,
                                'stock' => $request->variant_stocks[$index] ?? 0,
                                'weight' => $request->variant_weights[$index] ?? $product->weight,
                            ]);
                        }
                    }
                }
            });

            return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error creating product: ' . $e->getMessage());
        }
    }

    public function edit(Product $product): View
    {
        $categories = Category::all();
        $product->load(['categories', 'images', 'variants']);
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'weight' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'primary_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            // Variants
            'variant_ids.*' => 'nullable|integer',
            'variant_names.*' => 'nullable|string|max:100',
            'variant_skus.*' => 'nullable|string|max:100',
            'variant_prices.*' => 'nullable|numeric|min:0',
            'variant_stocks.*' => 'nullable|integer|min:0',
            'variant_weights.*' => 'nullable|integer|min:0',
        ]);

        try {
            DB::transaction(function () use ($request, $product) {
                $product->update([
                    'name' => $request->name,
                    'sku' => $request->sku,
                    'price' => $request->price,
                    'compare_at_price' => $request->compare_at_price,
                    'cost_price' => $request->cost_price,
                    'weight' => $request->weight,
                    'stock' => $request->stock,
                    'description' => $request->description,
                    'short_description' => $request->short_description,
                    'is_active' => $request->has('is_active'),
                    'is_featured' => $request->has('is_featured'),
                ]);

                // Sync categories
                $product->categories()->sync($request->categories);

                // Upload primary image
                if ($request->hasFile('primary_image')) {
                    // Delete old primary
                    $oldPrimary = ProductImage::where('product_id', $product->id)->where('is_primary', true)->first();
                    if ($oldPrimary) {
                        Storage::disk('public')->delete($oldPrimary->image_path);
                        $oldPrimary->delete();
                    }

                    $path = $request->file('primary_image')->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,
                        'is_primary' => true,
                        'sort_order' => 0,
                    ]);
                }

                // Upload extra images
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $index => $file) {
                        $path = $file->store('products', 'public');
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $path,
                            'is_primary' => false,
                            'sort_order' => $index + 5,
                        ]);
                    }
                }

                // Sync/Update variants
                $keepVariantIds = [];
                if ($request->filled('variant_names')) {
                    foreach ($request->variant_names as $index => $vName) {
                        if (!empty($vName)) {
                            $vId = $request->variant_ids[$index] ?? null;
                            $variantData = [
                                'name' => $vName,
                                'sku' => $request->variant_skus[$index] ?? ($product->sku . '-' . strtoupper(Str::random(4))),
                                'price' => $request->variant_prices[$index] ?? $product->price,
                                'stock' => $request->variant_stocks[$index] ?? 0,
                                'weight' => $request->variant_weights[$index] ?? $product->weight,
                            ];

                            if ($vId) {
                                $variant = ProductVariant::where('product_id', $product->id)->findOrFail($vId);
                                $variant->update($variantData);
                                $keepVariantIds[] = $vId;
                            } else {
                                $newVariant = ProductVariant::create(array_merge(['product_id' => $product->id], $variantData));
                                $keepVariantIds[] = $newVariant->id;
                            }
                        }
                    }
                }

                // Delete variants not sent in request
                ProductVariant::where('product_id', $product->id)
                    ->whereNotIn('id', $keepVariantIds)
                    ->delete();
            });

            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error updating product: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product soft deleted.');
    }
}
