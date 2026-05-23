<?php

namespace App\DTOs;

readonly class CartItemDTO
{
    public function __construct(
        public int $productId,
        public ?int $productVariantId,
        public int $quantity
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            productId: (int) $data['product_id'],
            productVariantId: isset($data['product_variant_id']) ? (int) $data['product_variant_id'] : null,
            quantity: (int) ($data['quantity'] ?? 1)
        );
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'product_variant_id' => $this->productVariantId,
            'quantity' => $this->quantity,
        ];
    }
}
