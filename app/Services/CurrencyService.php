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

    public function getCurrencyApi(): bool
    {
        try {
            $response = Http::connectTimeout(2)->timeout(4)->get($this->apiUrl);
        } catch (ConnectionException $e) {
            report($e);
            return false;
        }

        $currencies = $response->json();

        if (!$response->successful() || !is_array($currencies)) {
            Log::warning(!$response->successful()
                ? 'La api respondió con error' . $response->status()
                : 'La respuesta de la API no tiene el formato esperado.');
            return false;
        }

        $count = $this->saveCurrency($currencies);

        return $count == 3;
    }

    public function saveCurrency(array $currencies): int
    {
        $saved = 0;
        if (is_array($currencies)) {
            foreach ($currencies as $item) {

                if (
                    !isset($item['quote'], $item['date'], $item['rate'])
                    || !is_string($item['quote'])
                    || $item['quote'] === ''
                    || !is_numeric($item['rate'])
                    || (float) $item['rate'] <= 0
                ) {
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
