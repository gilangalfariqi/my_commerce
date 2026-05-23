<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $banners = Banner::active()->orderBy('sort_order')->get();
        $categories = Category::withCount('products')->get();
        
        $flashSale = FlashSale::active()->with('items.product.primaryImage')->first();
        
        $featuredProducts = Product::active()
            ->featured()
            ->with(['primaryImage', 'variants', 'categories'])
            ->latest()
            ->take(8)
            ->get();

        $latestProducts = Product::active()
            ->with(['primaryImage', 'variants', 'categories'])
            ->latest()
            ->take(8)
            ->get();

        return view('frontend.home', compact('banners', 'categories', 'flashSale', 'featuredProducts', 'latestProducts'));
    }
}
