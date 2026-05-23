<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAllActive(
        int $perPage = 12,
        ?string $search = null,
        ?int $categoryId = null,
        ?string $sortBy = null
    ): LengthAwarePaginator {
        $query = Product::active()
            ->with(['primaryImage', 'categories']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (!empty($categoryId)) {
            // Find category and its children to filter products in subcategories too
            $categoryIds = Cache::remember("category_and_children_{$categoryId}", 3600, function () use ($categoryId) {
                $ids = [$categoryId];
                $childrenIds = Category::where('parent_id', $categoryId)->pluck('id')->toArray();
                return array_merge($ids, $childrenIds);
            });

            $query->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            });
        }

        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function findActiveBySlug(string $slug): ?Product
    {
        return Product::active()
            ->with(['images', 'categories', 'variants'])
            ->where('slug', $slug)
            ->first();
    }

    public function findActiveById(int $id): ?Product
    {
        return Product::active()
            ->with(['images', 'categories', 'variants'])
            ->where('id', $id)
            ->first();
    }

    public function getFeatured(int $limit = 8): Collection
    {
        return Cache::remember("products_featured_{$limit}", 1800, function () use ($limit) {
            return Product::active()
                ->featured()
                ->with(['primaryImage'])
                ->limit($limit)
                ->get();
        });
    }

    public function searchAutocomplete(string $query, int $limit = 5): Collection
    {
        if (empty($query)) {
            return collect();
        }

        return Product::active()
            ->select('id', 'name', 'slug', 'price', 'compare_at_price')
            ->with(['primaryImage'])
            ->where('name', 'like', "%{$query}%")
            ->limit($limit)
            ->get();
    }
}
