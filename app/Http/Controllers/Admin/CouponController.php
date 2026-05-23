<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class CouponController extends Controller
{
    public function index(): View
    {
        $coupons = Coupon::latest()->paginate(15);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create(): View
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'type' => 'required|string|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'min_spend' => 'required|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'limit_per_user' => 'required|integer|min:1',
            'limit_total' => 'nullable|integer|min:1',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'is_active' => 'nullable|boolean',
        ]);

        Coupon::create([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'min_spend' => $request->min_spend,
            'max_discount' => $request->max_discount,
            'limit_per_user' => $request->limit_per_user,
            'limit_total' => $request->limit_total,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');
    }

    public function edit(Coupon $coupon): View
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'type' => 'required|string|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'min_spend' => 'required|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'limit_per_user' => 'required|integer|min:1',
            'limit_total' => 'nullable|integer|min:1',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'is_active' => 'nullable|boolean',
        ]);

        $coupon->update([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'min_spend' => $request->min_spend,
            'max_discount' => $request->max_discount,
            'limit_per_user' => $request->limit_per_user,
            'limit_total' => $request->limit_total,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted successfully.');
    }
}
