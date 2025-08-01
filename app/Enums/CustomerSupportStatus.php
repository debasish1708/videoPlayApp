<?php

namespace App\Enums;

enum CustomerSupportStatus : string
{
    case PENDING = 'pending';
    case RESOLVED = 'resolved';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::RESOLVED => 'Resolved',
        };
    }
}
