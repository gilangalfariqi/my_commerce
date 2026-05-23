<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending Payment',
            self::PROCESSING => 'Processing Order',
            self::SHIPPED => 'Shipped',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            self::PROCESSING => 'bg-blue-100 text-blue-800 border-blue-200',
            self::SHIPPED => 'bg-purple-100 text-purple-800 border-purple-200',
            self::COMPLETED => 'bg-green-100 text-green-800 border-green-200',
            self::CANCELLED => 'bg-red-100 text-red-800 border-red-200',
        };
    }
}
