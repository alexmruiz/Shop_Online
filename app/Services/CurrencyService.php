<?php

namespace App\Services;

use App\Enums\Currency;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{

    protected string $apiUrl = 'https://api.frankfurter.dev/v2/rates?quotes='
        . Currency::GBP->value . ','
        . Currency::USD->value . ','
        . Currency::JPY->value;

    /**
     * Obtiene los tipos de cambio de la API y los guarda en caché.
     *
     * @return bool True si se actualizan todos los tipos de cambio esperados; false en caso contrario.
     */
    public function getCurrencyApi(): bool
    {
        try {
            $response = Http::connectTimeout(2)->timeout(4)->get($this->apiUrl);
        } catch (ConnectionException $e) {
            report($e);
            return false;
        }

        $exchangeRates = $response->json();

        if (!$response->successful() || !is_array($exchangeRates)) {
            Log::warning(!$response->successful()
                ? 'La api respondió con error' . $response->status()
                : 'La respuesta de la API no tiene el formato esperado.');
            return false;
        }

        $count = $this->saveCurrency($exchangeRates);


        return $count === 3;
    }

    /**
     * Valida las cotizaciones recibidas y guarda en caché las válidas.
     *
     * @param array<int, array{date: string, base: string, quote: string, rate: int|float|string}> $exchangeRates
     * @return int Número de cotizaciones guardadas en caché.
     */
    public function saveCurrency(array $exchangeRates): int
    {
        $saved = 0;
        if (is_array($exchangeRates)) {
            foreach ($exchangeRates as $item) {

                if (
                    !isset($item['quote'], $item['date'], $item['rate'])
                    || !is_string($item['quote'])
                    || !in_array(strtolower($item['quote']), [
                        Currency::GBP->value,
                        Currency::USD->value,
                        Currency::JPY->value,
                    ], true)
                    || !is_numeric($item['rate'])
                    || (float) $item['rate'] <= 0
                    || (strtolower($item['base'])) != Currency::EUR->value
                ) {
                    Log::error('No se cumple la validación.', $item);
                    continue;
                }
                Cache::put($item['quote'], ['date' => $item['date'], 'rate' => $item['rate']], now()->addDays(7));
                Log::info('Tipos de cambio actualizados.', ['saved' => $item]);
                $saved++;
            }
        }
        return $saved;
    }

    public function getLastCurrencyCache(Currency $currency): ?array
    {
        return Cache::get($currency->value);
    }
}
