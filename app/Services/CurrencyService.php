<?php

namespace App\Services;

use App\Enums\Currency;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyService
{

    protected string $apiUrl = 'https://api.frankfurter.dev/v2/rates?quotes=usd,gbp,jpy';

    public function getCurrencyApi(): void
    {
        try {
            $response = Http::connectTimeout(2)->timeout(4)->get($this->apiUrl);
        } catch (ConnectionException $e) {
            report($e);
            return;
        }

        if (!$response->successful()) {
            return;
        }

        $this->saveCurrency($response->json());
    }

    public function saveCurrency(array $currencies): void
    {
        if (!empty($currencies)) {
            foreach ($currencies as $quote) {

                Cache::put($quote['quote'], $quote, now()->addDays(7));
            }
        }
    }

    public function getLastCurrencyCache(Currency $currency): ?array
    {
        return Cache::get($currency->value);
    }

 
}
