<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\Order;
use App\DTOs\CheckoutDTO;
use App\Enums\OrderStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function createFromCart(Cart $cart, CheckoutDTO $checkoutDto): Order;

    public function findById(string $id): ?Order;

    public function findByNumber(string $orderNumber): ?Order;

    public function getByUser(int $userId, int $perPage = 10): LengthAwarePaginator;

    public function updateStatus(string $orderId, OrderStatus $status): bool;
}
