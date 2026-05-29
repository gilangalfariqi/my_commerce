<?php

namespace App\DTOs;

readonly class CheckoutDTO
{
    public function __construct(
        public string $firstName,
        public ?string $lastName,
        public string $email,
        public string $phone,
        public int $provinceId,
        public string $provinceName,
        public int $cityId,
        public string $cityName,
        public string $district,
        public string $addressLine,
        public string $postalCode,
        public string $courier,
        public string $shippingService,
        public float $shippingCost,
        public ?string $notes,
        /**
         * For WhatsApp checkout workflow.
         * Example: 'whatsapp_checkout'
         */
        public string $checkoutChannel
    ) {}

    public static function fromRequest(array $data, float $shippingCost): self
    {
        return new self(
            firstName: $data['first_name'],
            lastName: $data['last_name'] ?? null,
            email: $data['email'],
            phone: $data['phone'],
            provinceId: (int) $data['province_id'],
            provinceName: $data['province_name'],
            cityId: (int) $data['city_id'],
            cityName: $data['city_name'],
            district: $data['district'] ?? ($data['city_name'] ?? ''),
            addressLine: $data['address_line'],
            postalCode: $data['postal_code'],
            courier: $data['courier'] ?? '',
            shippingService: $data['shipping_service'] ?? '',
            shippingCost: $shippingCost,
            notes: $data['notes'] ?? null,
            checkoutChannel: $data['checkout_channel'] ?? 'whatsapp_checkout',
        );
    }


    public function toShippingAddressArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
            'province_id' => $this->provinceId,
            'province_name' => $this->provinceName,
            'city_id' => $this->cityId,
            'city_name' => $this->cityName,
            'district' => $this->district,
            'address_line' => $this->addressLine,
            'postal_code' => $this->postalCode,
        ];
    }
}
