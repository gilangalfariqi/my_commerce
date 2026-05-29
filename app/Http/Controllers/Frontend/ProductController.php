<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BikeBrand;
use App\Models\BikeModel;
use App\Models\Category;
use App\Models\Product;
use App\Services\SEO\MetaService;
use App\Services\SEO\SchemaService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * MotoPartHub Catalog Controller
 *
 * Handles the public-facing product catalog, product detail page,
 * and Scout-powered search autocomplete endpoint.
 *
 * Filtering strategy:
 *   - Uses Spatie Laravel Query Builder exclusively — no manual if/else/where.
 *   - Vehicle fitment (brand/model/year) uses named model scopes.
 *   - Price range uses separate min_price / max_price scope filters.
 *   - All sort options are whitelisted via AllowedSort.
 */
class ProductController extends Controller
{
    public function __construct(
        protected MetaService $metaService,
        protected SchemaService $schemaService,
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // Catalog Index — Server-side fallback (Livewire primary)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Display the product catalog index.
     *
     * The Livewire `ProductFilter` component handles dynamic filtering
     * client-side. This server-side method is the non-JS fallback and
     * is also used for direct URL deep-links with query parameters.
     */
    public function index(Request $request): View
    {
        // Resolve active filters from clean query parameters for SEO purposes
        $brandSlug = $request->query('brand');
        $modelSlug = $request->query('model');
        $categorySlug = $request->query('category');

        $activeBrand = $brandSlug 
            ? BikeBrand::where('slug', $brandSlug)->first()
            : null;

        $activeModel = $modelSlug
            ? BikeModel::where('slug', $modelSlug)->with('bikeBrand')->first()
            : null;

        $activeCategory = $categorySlug
            ? Category::where('slug', $categorySlug)->first()
            : null;

        // SEO — dynamic meta based on active filters
        if ($activeBrand) {
            $this->metaService->setForFitment($activeBrand, $activeModel);
        } elseif ($activeCategory) {
            $this->metaService->setForCategory($activeCategory);
        } else {
            // Generic catalog page (no specific filter) — use catalog-specific SEO
            $this->metaService->setForCatalog();
        }

        // Fast active product count for general capacity statistic
        $totalActiveCount = Product::active()->count();

        return view('frontend.product.index', compact(
            'totalActiveCount',
            'activeBrand',
            'activeModel',
            'activeCategory'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Product Detail Page (PDP)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Display the Product Detail Page for a given slug.
     *
     * Increments `views_count` atomically to avoid race conditions.
     * Loads Spatie media conversions for the HD image zoom viewer.
     */
    public function show(string $slug, Request $request): View
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with([
                'categories',
                'variants',
                'fitments.bikeBrand',
                'media',
            ])
            ->firstOrFail();

        // Atomic increment — avoids N+1 update issue under high traffic
        Product::withoutTimestamps(fn () => $product->increment('views_count'));

        // Build WhatsApp CTA with optional fitment context from query params
        $fitmentContext = array_filter([
            'brand' => $request->query('brand'),
            'model' => $request->query('model'),
            'year'  => $request->query('year'),
        ]);

        $whatsAppLink = $product->getWhatsAppLink($fitmentContext ?: null);

        // Related products in the same category, excluding current product
        $categoryIds = $product->categories->pluck('id')->toArray();
        $relatedProducts = Product::active()
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds))
            ->where('id', '!=', $product->id)
            ->with(['media'])
            ->inRandomOrder()
            ->take(4)
            ->get();

        // SEO
        $this->metaService->setForProduct($product);
        $productSchema = $this->schemaService->productSchema($product);

        $firstCategory = $product->categories->first();
        $breadcrumb = $this->schemaService->breadcrumbSchema([
            ['name' => 'Home', 'url' => url('/')],
            ['name' => $firstCategory?->name ?? 'Katalog', 'url' => route('products.index', ['category' => $firstCategory?->slug])],
            ['name' => $product->name, 'url' => route('products.show', $product->slug)],
        ]);

        return view('frontend.product.show', compact(
            'product',
            'relatedProducts',
            'whatsAppLink',
            'productSchema',
            'breadcrumb',
            'fitmentContext'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Scout Search Autocomplete API
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * JSON endpoint for the search autocomplete dropdown.
     *
     * Uses Laravel Scout for search. When Scout driver is `array` (local dev),
     * it falls back to a safe LIKE query so development remains frictionless.
     *
     * Rate-limited to 60 requests/minute per IP via `throttle:60,1` middleware.
     */
    public function searchAutocomplete(Request $request): JsonResponse
    {
        $query = trim($request->get('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json([]);
        }

        try {
            // Scout search — works with Meilisearch (prod) and array driver (dev)
            $products = Product::search($query)
                ->query(fn ($q) => $q->active()->with('media')->take(8))
                ->take(8)
                ->get();
        } catch (\Exception $e) {
            // Graceful fallback to LIKE search if Scout/Meilisearch is unavailable
            $products = Product::active()
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('sku', 'like', "%{$query}%");
                })
                ->with('media')
                ->take(8)
                ->get();
        }

        $results = $products->map(fn (Product $product) => [
            'id'    => $product->id,
            'name'  => $product->name,
            'sku'   => $product->sku,
            'slug'  => $product->slug,
            'price' => 'Rp ' . number_format($product->getFinalPrice(), 0, ',', '.'),
            'image' => $product->thumbnail_url,
            'url'   => route('products.show', $product->slug),
        ]);

        return response()->json($results);
    }
}
