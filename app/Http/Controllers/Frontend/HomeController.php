<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\Product;
use App\Models\Setting;
use App\Services\SEO\MetaService;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    protected MetaService $metaService;

    public function __construct(MetaService $metaService)
    {
        $this->metaService = $metaService;
    }

    public function index(): View
    {
        $this->metaService->setDefault();

        // Banners — admin-managed, ordered by sort_order
        $banners = Banner::active()->orderBy('sort_order')->get();

        // Categories — only top-level active ones, ordered, with product count
        $categories = Category::active()
            ->ordered()
            ->withCount('products')
            ->whereNull('parent_id')
            ->get();

        // Flash Sale — active, eager-load items with product media (Spatie)
        $flashSale = FlashSale::active()
            ->with([
                'items' => fn ($q) => $q->with([
                    'product' => fn ($pq) => $pq->with('media'),
                ]),
            ])
            ->first();

        // Featured Products — is_featured=true, Spatie media, variants, categories
        $featuredProducts = Product::active()
            ->featured()
            ->with(['media', 'variants', 'categories'])
            ->latest()
            ->take(8)
            ->get();

        // Latest Products — newest first, Spatie media, variants, categories
        $latestProducts = Product::active()
            ->with(['media', 'variants', 'categories'])
            ->latest()
            ->take(8)
            ->get();

        // Load all settings from DB and pass to view
        $settings = Setting::all()->pluck('value', 'key')->reject(fn($val) => $val === null || $val === '');

        return view('frontend.home', compact(
            'banners',
            'categories',
            'flashSale',
            'featuredProducts',
            'latestProducts',
            'settings'
        ));
    }
}
