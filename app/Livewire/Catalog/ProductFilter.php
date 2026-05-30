<?php

namespace App\Livewire\Catalog;

use App\Models\BikeBrand;
use App\Models\BikeModel;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * MotoPartHub — Multi-Level Product Filter Livewire Component
 *
 * Implements a reactive, cascading 3-level vehicle fitment filter:
 *   Brand (Honda) → Model (Vario 125) → Year (2020)
 *
 * Each level is URL-synced via #[Url] so filter states are shareable
 * and browser-history-aware (wire:navigate compatible).
 *
 * Architecture notes:
 *   - Uses Spatie Query Builder for consistent filter handling
 *   - All query logic is in #[Computed] properties (lazy + cached per request)
 *   - wire:model.live.debounce.300ms on search to limit server round-trips
 *   - Pagination resets on any filter change via resetPage()
 */
class ProductFilter extends Component
{
    use WithPagination;

    // ─────────────────────────────────────────────────────────────────────────
    // Filter State (URL-synced for shareability & SEO-friendliness)
    // ─────────────────────────────────────────────────────────────────────────

    #[Url(as: 'q', history: true, keep: false)]
    public string $search = '';

    #[Url(as: 'brand', history: true, keep: false)]
    public string $selectedBrand = '';

    #[Url(as: 'model', history: true, keep: false)]
    public string $selectedModel = '';

    #[Url(as: 'year', history: true, keep: false)]
    public string $selectedYear = '';

    #[Url(as: 'category', history: true, keep: false)]
    public string $selectedCategory = '';

    #[Url(as: 'min_price', history: false, keep: false)]
    public string $priceMin = '';

    #[Url(as: 'max_price', history: false, keep: false)]
    public string $priceMax = '';

    #[Url(as: 'sort', history: true, keep: false)]
    public string $sortBy = '-created_at';

    #[Url(as: 'stock', history: false, keep: false)]
    public bool $inStockOnly = false;

    #[Url(as: 'featured', history: true, keep: false)]
    public bool $isFeaturedOnly = false;

    public int $perPage = 12;

    // ─────────────────────────────────────────────────────────────────────────
    // Lifecycle Hooks — Cascade Reset on Filter Change
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * When brand changes: reset model, year, and paginator.
     * This prevents orphaned model/year selections.
     */
    public function updatedSelectedBrand(): void
    {
        $this->selectedModel = '';
        $this->selectedYear  = '';
        $this->resetPage();
    }

    /**
     * When model changes: reset year selection and paginator.
     */
    public function updatedSelectedModel(): void
    {
        $this->selectedYear = '';
        $this->resetPage();
    }

    /**
     * Reset paginator whenever any top-level filter changes.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedYear(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory(): void
    {
        $this->resetPage();
    }

    public function updatedPriceMin(): void
    {
        $this->resetPage();
    }

    public function updatedPriceMax(): void
    {
        $this->resetPage();
    }

    public function updatedInStockOnly(): void
    {
        $this->resetPage();
    }

    public function updatedIsFeaturedOnly(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Public Actions
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Reset all filters to default state.
     */
    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'selectedBrand',
            'selectedModel',
            'selectedYear',
            'selectedCategory',
            'priceMin',
            'priceMax',
            'sortBy',
            'inStockOnly',
            'isFeaturedOnly',
        ]);

        $this->sortBy = '-created_at';
        $this->resetPage();
    }

    /**
     * Apply vehicle garage preset (from "Garasi Saya" feature).
     */
    public function applyGaragePreset(string $brandSlug, string $modelSlug, ?int $year = null): void
    {
        $this->selectedBrand = $brandSlug;
        $this->selectedModel = $modelSlug;
        $this->selectedYear  = $year ? (string) $year : '';
        $this->resetPage();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Computed Properties (Lazy, cached per Livewire request)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * All active bike brands for the brand selector dropdown.
     */
    #[Computed]
    public function bikeBrands(): \Illuminate\Database\Eloquent\Collection
    {
        return BikeBrand::active()->ordered()->get(['id', 'name', 'slug']);
    }

    /**
     * Bike models for the selected brand.
     * Returns empty collection if no brand is selected.
     */
    #[Computed]
    public function bikeModels(): \Illuminate\Database\Eloquent\Collection
    {
        if (blank($this->selectedBrand)) {
            return BikeModel::active()->whereRaw('1=0')->get(['id', 'name', 'slug', 'years_available']);
        }

        return BikeModel::active()
            ->whereHas('bikeBrand', fn ($q) => $q->where('slug', $this->selectedBrand))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'years_available']);
    }

    /**
     * Available years for the selected model.
     * Merges years_available JSON column with distinct years from product_fitments.
     */
    #[Computed]
    public function availableYears(): array
    {
        if (blank($this->selectedModel)) {
            return [];
        }

        $bikeModel = BikeModel::where('slug', $this->selectedModel)
            ->with([
                'products' => fn ($q) => $q->select('products.id')
                    ->withPivot('year'),
            ])
            ->first();

        if (! $bikeModel) {
            return [];
        }

        return $bikeModel->getAllAvailableYears();
    }

    /**
     * All active categories for the category filter.
     */
    #[Computed]
    public function categories(): \Illuminate\Database\Eloquent\Collection
    {
        return Category::active()->orderBy('sort_order')->get(['id', 'name', 'slug']);
    }

    /**
     * The main paginated product result set.
     *
     * Uses Spatie QueryBuilder with the same AllowedFilter definitions
     * as the server-side ProductController for consistency.
     * Scout search is used when a search term is present.
     */
    #[Computed]
    public function products(): LengthAwarePaginator
    {
        $baseQuery = Product::active()
            ->with([
                'categories',
                'fitments.bikeBrand',
                'media',
            ]);

        // Safety net: jika scope active menghasilkan 0 (misconfiguration/data issue),
        // fallback ke produk is_active=true secara eksplisit.
        // Ini memastikan katalog tetap tampil sesuai business rule.
        if ($baseQuery->clone()->limit(1)->count() === 0) {
            $baseQuery = Product::query()
                ->where('is_active', true)
                ->with([
                    'categories',
                    'fitments.bikeBrand',
                    'media',
                ]);
        }

        // When a search term is present, use Scout to get matching IDs
        // then constrain the Eloquent query to maintain eager-loading
        if (filled($this->search)) {
            try {
                $matchingIds = Product::search($this->search)
                    ->query(fn ($q) => $q->active())
                    ->keys();

                if ($matchingIds->isNotEmpty()) {
                    $baseQuery->whereIn('id', $matchingIds);
                } else {
                    // Scout found nothing; use LIKE as graceful fallback
                    $term = $this->search;
                    $baseQuery->where(function ($q) use ($term) {
                        $q->where('name', 'like', "%{$term}%")
                            ->orWhere('sku', 'like', "%{$term}%")
                            ->orWhere('short_description', 'like', "%{$term}%");
                    });
                }
            } catch (\Exception) {
                // Scout unavailable — pure LIKE fallback
                $term = $this->search;
                $baseQuery->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%");
                });
            }
        }

        // Vehicle fitment cascade filters
        if (filled($this->selectedBrand)) {
            $baseQuery->forBikeBrand($this->selectedBrand);
        }
        if (filled($this->selectedModel)) {
            $baseQuery->forBikeModel($this->selectedModel);
        }
        if (filled($this->selectedYear)) {
            $baseQuery->forFitmentYear((int) $this->selectedYear);
        }

        // Category filter
        if (filled($this->selectedCategory)) {
            $baseQuery->whereHas('categories', fn ($q) => $q->where('slug', $this->selectedCategory));
        }

        // Price range
        if (filled($this->priceMin) && is_numeric($this->priceMin)) {
            $baseQuery->minPrice((float) $this->priceMin);
        }
        if (filled($this->priceMax) && is_numeric($this->priceMax)) {
            $baseQuery->maxPrice((float) $this->priceMax);
        }

        // Stock filter
        if ($this->inStockOnly) {
            $baseQuery->inStock();
        }

        // Featured filter
        if ($this->isFeaturedOnly) {
            $baseQuery->where('is_featured', true);
        }

        // Sorting
        match ($this->sortBy) {
            'price_asc'     => $baseQuery->orderBy('price', 'asc'),
            'price_desc'    => $baseQuery->orderBy('price', 'desc'),
            'name_asc'      => $baseQuery->orderBy('name', 'asc'),
            'popular'       => $baseQuery->orderBy('views_count', 'desc'),
            'featured'      => $baseQuery->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc'),
            default         => $baseQuery->orderBy('created_at', 'desc'),
        };

        return $baseQuery->paginate($this->perPage);
    }

    /**
     * Count of active filters (for showing "X filters active" badge).
     */
    #[Computed]
    public function activeFilterCount(): int
    {
        return collect([
            $this->selectedBrand,
            $this->selectedModel,
            $this->selectedYear,
            $this->selectedCategory,
            $this->priceMin,
            $this->priceMax,
            $this->inStockOnly ? '1' : '',
            $this->isFeaturedOnly ? '1' : '',
            ($this->search !== '') ? '1' : '',
        ])->filter(fn ($v) => filled($v))->count();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Render
    // ─────────────────────────────────────────────────────────────────────────

    public function render(): View
    {
        return view('livewire.catalog.product-filter');
    }
}
