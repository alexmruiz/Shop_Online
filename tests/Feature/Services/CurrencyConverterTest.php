<?php

namespace Tests\Feature\Services;

use App\Enums\Currency;
use App\Exceptions\CurrencyConverterService\RateNotvailable;
use App\Services\CurrencyConverterService;
use App\Services\CurrencyService;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CurrencyConverterTest extends TestCase
{
    private CurrencyConverterService $converter;
    private CurrencyService $currencyService;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        $this->currencyService = new CurrencyService();

        $this->converter = new CurrencyConverterService($this->currencyService);
    }

    #[Test]
    #[DataProvider('currencyConversions')]
    public function it_converts_eur_to_each_supported_currency(
        Currency $currency,
        string $rate,
        string $expected,
    ): void {
        Cache::put($currency->value, ['rate' => $rate]);

        $convertedAmount = $this->converter->convertFromEur($currency, '10.00');

        $this->assertSame($expected, $convertedAmount);
    }

    public static function currencyConversions(): array
    {
        return [
            'USD uses two decimal places' => [Currency::USD, '1.1202', '11.20'],
            'GBP uses two decimal places' => [Currency::GBP, '0.84534', '8.45'],
            'JPY rounds to whole yen' => [Currency::JPY, '177.46', '1775'],
        ];
    }

    #[Test]
    #[DataProvider('invalidCachedQuotes')]
    public function it_throws_when_the_cached_rate_is_missing_or_invalid(mixed $quote): void
    {
        Cache::put(Currency::USD->value, $quote);

        $this->expectException(RateNotvailable::class);

        $this->converter->convertFromEur(Currency::USD, '10.00');
    }

    public static function invalidCachedQuotes(): array
    {
        return [
            'missing quote' => [null],
            'missing rate' => [[]],
            'non-numeric rate' => [['rate' => 'invalid']],
        ];
    }
}
