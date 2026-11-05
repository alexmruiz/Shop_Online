<?php

namespace App\Services;

use App\Enums\Currency;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;

class CurrencyFormatService
{

    /**
     * Formatea un importe según la moneda indicada y el idioma del usuario autenticado
     * o, si no hay sesión, el idioma configurado para la aplicación.
     *
     * @param Currency $currency Moneda en la que se expresa el importe.
     * @param string $price Importe numérico que se desea formatear.
     * @return string Importe formateado con el símbolo y separadores adecuados.
     */
    public function formatMoney(Currency $currency, string $price): string
    {
        $langUser =  Auth::check() ? Auth::user()->lang : App::getLocale();
        return Number::currency($price, in: strtoupper($currency->value), locale: $langUser);
    }
}
