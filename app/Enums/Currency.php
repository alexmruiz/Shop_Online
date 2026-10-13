<?php

namespace App\Enums;

enum Currency: string
{
    case EUR = 'eur';
    case USD = 'usd';
    case GBP = 'gbp';
    case JPY = 'jpy';
}
