<?php

namespace Tests\Feature\Services;

use App\Enums\Currency;
use App\Services\CurrencyFormatService;
use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CurrencyFormatServiceTest extends TestCase
{
    #[Test]
    #[DataProvider('currencyFormats')]
    public function it_formats_amounts_using_the_application_locale(
        Currency $currency,
        string $expected,
    ): void {
        App::setLocale('es');

        $service = new CurrencyFormatService;

        $this->assertSame($expected, $service->formatMoney($currency, '10.00'));
    }

    public static function currencyFormats(): array
    {
        return [
            'EUR' => [Currency::EUR, "10,00\u{00A0}€"],
            'USD' => [Currency::USD, "10,00\u{00A0}US$"],
            'GBP' => [Currency::GBP, "10,00\u{00A0}GBP"],
            'JPY' => [Currency::JPY, "10\u{00A0}JPY"],
        ];
    }

    #[Test]
    public function it_formats_amounts_using_the_english_locale(): void
    {
        App::setLocale('en');

        $service = new CurrencyFormatService;

        $this->assertSame('$10.00', $service->formatMoney(Currency::USD, '10.00'));
    }
}
