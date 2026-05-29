<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BikeBrand;
use App\Models\BikeModel;
use App\Models\Category;
use App\Models\Product;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Symfony\Component\HttpFoundation\Response;

/**
 * MotoPartHub Sitemap Controller
 *
 * Generates a comprehensive XML sitemap using spatie/laravel-sitemap.
 * Covers all crawlable pages:
 *   - Static pages (home, catalog)
 *   - Product Detail Pages (PDPs)
 *   - Category-filtered catalog pages
 *   - Bike brand / model filtered catalog pages
 */
class SitemapController extends Controller
{
    public function index(): Response
    {
        $sitemap = Sitemap::create();

        // ── Static pages ────────────────────────────────────────────
        $sitemap->add(
            Url::create(route('home'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(1.0)
        );

        $sitemap->add(
            Url::create(route('products.index'))
                ->setLastModificationDate(now())
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
                ->setPriority(0.9)
        );

        // ── Categories ───────────────────────────────────────────────
        Category::active()
            ->orderBy('updated_at', 'desc')
            ->get(['slug', 'updated_at'])
            ->each(function (Category $category) use ($sitemap) {
                $sitemap->add(
                    Url::create(route('products.index', ['category' => $category->slug]))
                        ->setLastModificationDate($category->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.8)
                );
            });

        // ── Bike brands (fitment-filtered catalog pages) ─────────────
        BikeBrand::active()
            ->with('bikeModels:id,bike_brand_id,slug,updated_at')
            ->get(['id', 'slug', 'updated_at'])
            ->each(function (BikeBrand $brand) use ($sitemap) {
                // Brand-level page
                $sitemap->add(
                    Url::create(route('products.index', ['brand' => $brand->slug]))
                        ->setLastModificationDate($brand->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.7)
                );

                // Model-level pages
                $brand->bikeModels->each(function (BikeModel $model) use ($brand, $sitemap) {
                    $sitemap->add(
                        Url::create(route('products.index', [
                            'brand' => $brand->slug,
                            'model' => $model->slug,
                        ]))
                            ->setLastModificationDate($model->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority(0.6)
                    );
                });
            });

        // ── Products (PDPs) ──────────────────────────────────────────
        Product::active()
            ->orderBy('updated_at', 'desc')
            ->get(['slug', 'updated_at', 'is_featured'])
            ->each(function (Product $product) use ($sitemap) {
                $sitemap->add(
                    Url::create(route('products.show', $product->slug))
                        ->setLastModificationDate($product->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority($product->is_featured ? 0.95 : 0.85)
                );
            });

        return $sitemap->toResponse(request());
    }
}
