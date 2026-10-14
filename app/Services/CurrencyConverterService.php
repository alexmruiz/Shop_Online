<?php

namespace App\Services;

use App\Enums\Currency;
use App\Exceptions\CurrencyConverterService\CurrencyNotPermitted;
use App\Exceptions\CurrencyConverterService\RateNotvailable;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

class CurrencyConverterService
{
    private const INVALID_CACHED_RATE_MESSAGE = "Los datos obtenidos de la caché no se han podido validar";


    public function __construct(private CurrencyService $currencyService) {}

    /**
     * Convierte un precio desde Euros (EUR) a otra moneda utilizando la tasa de cambio almacenada en caché.
     *
     * @param Currency $currency Moneda de destino a la que se desea convertir.
     * @param string $price Precio en Euros que se va a convertir.
     * @return string|null El precio convertido con su escala correspondiente, o null si la moneda es EUR o los datos están vacíos.
     * @throws RateNotvailable Si la tasa de cambio en caché no es válida o no existe.
     */
    public function convertFromEur(Currency $currency, string $price): ?string
    {
        if (empty($price)) {
            return null;
        }

        if ($currency->value === Currency::EUR->value) {
            throw new CurrencyNotPermitted("No es posible convertir la moneda base");
        }

        $valueCurrency = $currency->value;

        $quote = $this->currencyService->getLastCurrencyCache($currency);

        if (!is_array($quote) || !isset($quote['rate']) || !is_numeric($quote['rate']) || $quote['rate'] <= 0) {
            throw new RateNotvailable(self::INVALID_CACHED_RATE_MESSAGE . $valueCurrency);
        }

        $scale = $currency === Currency::JPY ? 0 : 2;

        return (string) BigDecimal::of($price)
            ->multipliedBy((string) $quote['rate'])
            ->toScale($scale, RoundingMode::HALF_UP);
    }
}
