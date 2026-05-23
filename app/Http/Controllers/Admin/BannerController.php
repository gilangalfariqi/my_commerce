<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\View\View;

class BannerController extends Controller
{
    public function index(): View
    {
        $banners = Banner::orderBy('sort_order', 'asc')->paginate(10);
        return view('admin.banners.index', compact('banners'));
    }

    public function create(): View
    {
        return view('admin.banners.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image_path' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'click_url' => 'nullable|string|max:255',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $path = $request->file('image_path')->store('banners', 'public');

        Banner::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image_path' => $path,
            'click_url' => $request->click_url,
            'sort_order' => $request->sort_order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully.');
    }

    public function edit(Banner $banner): View
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image_path' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'click_url' => 'nullable|string|max:255',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $path = $banner->image_path;
        if ($request->hasFile('image_path')) {
            Storage::disk('public')->delete($banner->image_path);
            $path = $request->file('image_path')->store('banners', 'public');
        }

        $banner->update([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image_path' => $path,
            'click_url' => $request->click_url,
            'sort_order' => $request->sort_order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        Storage::disk('public')->delete($banner->image_path);
        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully.');
    }
}
