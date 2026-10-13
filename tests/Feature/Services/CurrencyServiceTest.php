<?php

namespace Tests\Feature\Services;

use App\Enums\Currency;
use App\Services\CurrencyService;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CurrencyServiceTest extends TestCase
{
    private CurrencyService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Http::swap(new Factory($this->app['events']));
        Cache::flush();
        $this->service = new CurrencyService();
    }

    #[Test]
    public function it_fetches_rates_from_frankfurter_and_caches_each_quote(): void
    {
        $rates = [
            [
                'date' => '2026-10-12',
                'base' => 'EUR',
                'quote' => 'usd',
                'rate' => 1.16,
            ],
            [
                'date' => '2026-10-12',
                'base' => 'EUR',
                'quote' => 'gbp',
                'rate' => 0.87,
            ],
        ];

        Http::fake([
            'api.frankfurter.dev/v2/rates*' => Http::response($rates),
        ]);

        $this->service->getCurrencyApi();

        $this->assertSame($rates[0], Cache::get('usd'));
        $this->assertSame($rates[1], Cache::get('gbp'));
        Http::assertSent(
            fn (Request $request): bool => $request->method() === 'GET'
                && $request->url() === 'https://api.frankfurter.dev/v2/rates?quotes=usd,gbp,jpy'
        );
    }

    #[Test]
    public function it_keeps_the_cached_rate_when_the_api_connection_fails(): void
    {
        $cachedRate = [
            'date' => '2026-10-09',
            'base' => 'EUR',
            'quote' => 'usd',
            'rate' => 1.15,
        ];
        Cache::put('usd', $cachedRate, now()->addDays(7));

        Http::fake([
            'api.frankfurter.dev/v2/rates*' => Http::failedConnection('Connection timed out'),
        ]);

        $this->service->getCurrencyApi();

        $this->assertSame($cachedRate, $this->service->getLastCurrencyCache(Currency::USD));
    }

    #[Test]
    public function it_keeps_the_cached_rate_when_the_api_returns_an_error_response(): void
    {
        $cachedRate = [
            'date' => '2026-10-09',
            'base' => 'EUR',
            'quote' => 'usd',
            'rate' => 1.15,
        ];
        Cache::put('usd', $cachedRate, now()->addDays(7));

        Http::fake([
            'api.frankfurter.dev/v2/rates*' => Http::response([], 503),
        ]);

        $this->service->getCurrencyApi();

        $this->assertSame($cachedRate, $this->service->getLastCurrencyCache(Currency::USD));
    }

    #[Test]
    public function it_returns_null_when_no_rate_is_cached_for_the_currency(): void
    {
        $this->assertNull($this->service->getLastCurrencyCache(Currency::USD));
    }
}
