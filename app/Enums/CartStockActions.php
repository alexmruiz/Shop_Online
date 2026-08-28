<?php

namespace App\Enums;

enum CartStockActions: string
{
    case INCREMENT = 'increment';
    case DECREMENT = 'decrement';
    case DELETE = 'delete';
}