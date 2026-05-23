<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case SETTLED = 'settled';
    case EXPIRED = 'expired';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::SETTLED => 'Paid',
            self::EXPIRED => 'Expired',
            self::FAILED => 'Failed',
            self::REFUNDED => 'Refunded',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            self::SETTLED => 'bg-green-100 text-green-800 border-green-200',
            self::EXPIRED => 'bg-orange-100 text-orange-800 border-orange-200',
            self::FAILED => 'bg-red-100 text-red-800 border-red-200',
            self::REFUNDED => 'bg-blue-100 text-blue-800 border-blue-200',
        };
    }
}
