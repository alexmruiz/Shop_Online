<?php

namespace Tests\Feature\Services;

use App\Enums\Currency;
use App\Models\User;
use App\Services\CurrencyFormatService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CurrencyFormatServiceTest extends TestCase
{
    #[Test]
    #[DataProvider('currencyFormats')]
    public function it_formats_amounts_using_the_authenticated_users_language(
        Currency $currency,
        string $expected,
    ): void {
        $user = new User;
        $user->lang = 'es';
        $this->actingAs($user);

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
    public function it_uses_the_application_locale_for_guests(): void
    {
        Auth::logout();
        App::setLocale('en');

        $service = new CurrencyFormatService;

        $this->assertSame('$10.00', $service->formatMoney(Currency::USD, '10.00'));
    }
}
