<?php

namespace App\Enums;

enum CartStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
}