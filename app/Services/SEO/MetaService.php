<?php

namespace App\Services\SEO;

use App\Models\BikeBrand;
use App\Models\BikeModel;
use App\Models\Category;
use App\Models\Product;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;

/**
 * MetaService — Centralized SEO meta tag management.
 *
 * Uses artesaos/seotools to generate <meta>, Open Graph, Twitter Cards,
 * and JSON-LD tags for all frontend pages.
 *
 * Usage: inject this service into controllers and call the appropriate
 * setFor*() method before returning the view.
 */
class MetaService
{
    protected string $siteName;
    protected string $appUrl;

    public function __construct()
    {
        $this->siteName = config('app.name', 'MotoPartHub');
        $this->appUrl   = config('app.url', 'http://localhost');
    }

    /**
     * Default / Homepage SEO.
     */
    public function setDefault(): void
    {
        $title = "{$this->siteName} — Sparepart Motor Premium Indonesia";
        $desc  = 'Destinasi utama suku cadang OEM & aftermarket motor terpercaya di Indonesia. '
                . 'Honda, Yamaha, Kawasaki, Suzuki & lebih banyak merek. Harga kompetitif, stok lengkap.';

        $this->applyMeta($title, $desc, url()->current());
        $this->applyOpenGraph($title, $desc, url()->current(), 'website');
        $this->applyTwitterCard($title, $desc);
    }

    /**
     * Product Detail Page (PDP) SEO.
     */
    public function setForProduct(Product $product): void
    {
        // Prefer Spatie Media Library HD conversion; fallback chain
        $image = $product->getFirstMediaUrl('product-images', 'hd')
            ?: $product->getFirstMediaUrl('product-images')
            ?: $this->defaultOgImage();

        $rawDesc = $product->short_description ?? strip_tags($product->description ?? '');
        $desc    = mb_substr(strip_tags($rawDesc), 0, 140);

        // Append fitment info to description (great for long-tail SEO)
        if ($product->fitments->isNotEmpty()) {
            $fitmentNames = $product->fitments
                ->take(3)
                ->map(fn ($m) => trim(($m->bikeBrand?->name ?? '') . ' ' . $m->name))
                ->implode(', ');
            $desc .= ' | Cocok untuk: ' . $fitmentNames;
        }

        $desc  = mb_substr($desc, 0, 155);
        $title = $product->name . ' (SKU: ' . $product->sku . ') — ' . $this->siteName;
        $url   = route('products.show', $product->slug);

        $keywords = array_filter([
            $product->name,
            $product->sku,
            'sparepart motor',
            'suku cadang',
            ...$product->fitments->take(3)->map(fn ($m) => $m->bikeBrand?->name . ' ' . $m->name)->toArray(),
            ...$product->categories->pluck('name')->toArray(),
        ]);

        $this->applyMeta($title, $desc, $url, $keywords);
        $this->applyOpenGraph($title, $desc, $url, 'product', $image);
        $this->applyTwitterCard($title, $desc, $image);
    }

    /**
     * Category-filtered catalog page SEO.
     */
    public function setForCategory(Category $category): void
    {
        $rawDesc = $category->description ?? "Jelajahi koleksi {$category->name} kami — suku cadang OEM & aftermarket berkualitas.";
        $desc    = mb_substr(strip_tags($rawDesc), 0, 155);
        $title   = $category->name . ' Sparepart Motor — ' . $this->siteName;
        $url     = route('products.index', ['category' => $category->slug]);

        $this->applyMeta($title, $desc, $url, [$category->name, 'sparepart motor', 'suku cadang']);
        $this->applyOpenGraph($title, $desc, $url, 'website');
        $this->applyTwitterCard($title, $desc);
    }

    /**
     * Vehicle-fitment filtered catalog page SEO.
     * Generates rich, keyword-targeted titles for motorcycle brand/model pages.
     * Example: "Sparepart Honda Vario 125 — MotoPartHub"
     */
    public function setForFitment(BikeBrand $brand, ?BikeModel $bikeModel = null): void
    {
        if ($bikeModel) {
            $title     = "Sparepart {$brand->name} {$bikeModel->name} — {$this->siteName}";
            $desc      = "Temukan sparepart OEM & aftermarket untuk {$brand->name} {$bikeModel->name}. ";
            $desc     .= 'Filter berdasarkan tahun, kategori, dan ketersediaan stok.';
            $canonical = route('products.index', [
                'brand' => $brand->slug,
                'model' => $bikeModel->slug,
            ]);
            $keywords  = [$brand->name, $bikeModel->name, 'sparepart motor', 'suku cadang'];
        } else {
            $models    = $brand->bikeModels->take(5)->pluck('name')->implode(', ');
            $title     = "Sparepart Motor {$brand->name} — {$this->siteName}";
            $desc      = "Temukan semua suku cadang & sparepart {$brand->name}: {$models}. Kualitas terjamin, harga kompetitif.";
            $canonical = route('products.index', ['brand' => $brand->slug]);
            $keywords  = [$brand->name, 'sparepart motor', 'suku cadang motor'];
        }

        $this->applyMeta($title, mb_substr($desc, 0, 155), $canonical, $keywords);
        $this->applyOpenGraph($title, mb_substr($desc, 0, 155), $canonical, 'website');
        $this->applyTwitterCard($title, mb_substr($desc, 0, 155));
    }

    /**
     * General catalog page (no specific filter active).
     */
    public function setForCatalog(): void
    {
        $title = "Katalog Sparepart Motor — {$this->siteName}";
        $desc  = 'Temukan ribuan suku cadang OEM & aftermarket untuk Honda, Yamaha, Kawasaki, Suzuki, '
               . 'dan banyak merek lainnya. Filter berdasarkan kendaraan Anda untuk hasil yang tepat.';

        $this->applyMeta($title, mb_substr($desc, 0, 155), route('products.index'), [
            'sparepart motor',
            'suku cadang motor',
            'katalog sparepart',
            'parts motor Indonesia',
        ]);
        $this->applyOpenGraph($title, mb_substr($desc, 0, 155), route('products.index'), 'website');
        $this->applyTwitterCard($title, mb_substr($desc, 0, 155));
    }

    /**
     * Custom / arbitrary page SEO.
     */
    public function setCustom(
        string $title,
        string $description,
        ?string $canonical = null,
        ?string $image = null,
        array $keywords = []
    ): void {
        $fullTitle = $title . ' — ' . $this->siteName;
        $desc      = mb_substr($description, 0, 155);
        $url       = $canonical ?? url()->current();

        $this->applyMeta($fullTitle, $desc, $url, $keywords);
        $this->applyOpenGraph($fullTitle, $desc, $url, 'website', $image ?? $this->defaultOgImage());
        $this->applyTwitterCard($fullTitle, $desc, $image);
    }

    // ──────────────────────────────────────────────────────────────
    // Private Helpers
    // ──────────────────────────────────────────────────────────────

    /**
     * Apply SEOMeta (title, description, canonical, robots, keywords).
     */
    private function applyMeta(string $title, string $description, string $canonical, array $keywords = []): void
    {
        SEOMeta::setTitle($title);
        SEOMeta::setDescription($description);
        SEOMeta::setCanonical($canonical);
        SEOMeta::setRobots('index, follow');

        $defaultKeywords = ['sparepart motor', 'suku cadang', $this->siteName];
        SEOMeta::addKeyword(array_unique(array_merge($defaultKeywords, $keywords)));
    }

    /**
     * Apply Open Graph tags.
     */
    private function applyOpenGraph(
        string $title,
        string $description,
        string $url,
        string $type = 'website',
        ?string $image = null
    ): void {
        OpenGraph::setTitle($title);
        OpenGraph::setDescription($description);
        OpenGraph::setType($type);
        OpenGraph::setUrl($url);
        OpenGraph::setSiteName($this->siteName);
        OpenGraph::addProperty('locale', 'id_ID');

        $imgUrl = $image ?? $this->defaultOgImage();
        if ($imgUrl) {
            OpenGraph::addImage($imgUrl, ['width' => 1200, 'height' => 630]);
        }
    }

    /**
     * Apply Twitter Card tags.
     */
    private function applyTwitterCard(string $title, string $description, ?string $image = null): void
    {
        TwitterCard::setType('summary_large_image');
        TwitterCard::setTitle($title);
        TwitterCard::setDescription($description);

        $imgUrl = $image ?? $this->defaultOgImage();
        if ($imgUrl) {
            TwitterCard::setImage($imgUrl);
        }
    }

    /**
     * Default OG/Twitter share image URL.
     */
    private function defaultOgImage(): ?string
    {
        $path = public_path('images/og-default.webp');
        if (file_exists($path)) {
            return $this->appUrl . '/images/og-default.webp';
        }
        return null;
    }
}
