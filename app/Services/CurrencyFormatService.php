<?php

namespace App\Services;

use App\Enums\Currency;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Number;

class CurrencyFormatService
{

    /**
     * Formatea un importe según la moneda indicada y el idioma del usuario.
     *
     * @param Currency $currency Moneda en la que se expresa el importe.
     * @param string $price Importe numérico que se desea formatear.
     * @return string Importe formateado con el símbolo y separadores adecuados.
     */
    public function formatMoney(Currency $currency, string $price): string
    {
        return Number::currency($price, in: strtoupper($currency->value), locale:  App::getLocale());
    }
}
