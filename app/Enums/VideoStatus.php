<?php

namespace App\Enums;

enum VideoStatus : string
{
    case UNPUBLISHED = 'unpublished';
    case PUBLISHED = 'published';
    case PROCESSING = 'processing';
    case FAILED = 'failed';
    case DELETED = 'deleted';

    public function label(): string
    {
        return match ($this) {
            self::UNPUBLISHED => 'Unpublished',
            self::PUBLISHED => 'Published',
            self::PROCESSING => 'Processing',
            self::FAILED => 'Failed',
            self::DELETED => 'Deleted',
        };
    }
}
