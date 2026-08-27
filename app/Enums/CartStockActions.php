<?php

namespace App\Enums;

enum CartStockActions: string
{
    case INCREMENT = 'pending';
    case DECREMENT = 'processing';
    case DELETE = 'confirmed';
}