<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    public function getAllActive(
        int $perPage = 12,
        ?string $search = null,
        ?int $categoryId = null,
        ?string $sortBy = null
    ): LengthAwarePaginator;

    public function findActiveBySlug(string $slug): ?Product;

    public function findActiveById(int $id): ?Product;

    public function getFeatured(int $limit = 8): Collection;

    public function searchAutocomplete(string $query, int $limit = 5): Collection;
}
