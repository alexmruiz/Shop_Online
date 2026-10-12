<?php

namespace Tests\Feature\Services;

use App\Services\PostalCodeService;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PostalCodeServiceTest extends TestCase
{
    private PostalCodeService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Http::swap(new Factory($this->app['events']));
        $this->service = new PostalCodeService();
    }

    #[Test]
    public function it_returns_cities_and_province_for_a_successful_lookup(): void
    {
        Http::fake([
            'api.zippopotam.us/es/29001' => Http::response([
                'places' => [
                    [
                        'place name' => 'Málaga',
                        'state' => 'Andalucía',
                    ],
                    [
                        'place name' => 'Alhaurín de la Torre',
                        'state' => 'Andalucía',
                    ],
                ],
            ]),
        ]);

        $result = $this->service->updatedPostalCode('29001');

        $this->assertSame([
            'cities' => [
                'Málaga' => 'Málaga',
                'Alhaurín de la Torre' => 'Alhaurín de la Torre',
            ],
            'province' => 'Andalucía',
            'available' => true,
        ], $result);

        Http::assertSent(
            fn (Request $request): bool => $request->url() === 'https://api.zippopotam.us/es/29001'
        );
    }

    #[Test]
    public function it_returns_not_found_when_the_api_rejects_the_postal_code(): void
    {
        Http::fake([
            'api.zippopotam.us/es/00000' => Http::response([], 404),
        ]);

        $result = $this->service->updatedPostalCode('00000');

        $this->assertSame([
            'available' => true,
            'cities' => [],
            'province' => '',
            'found' => false,
        ], $result);
    }

    #[Test]
    public function it_returns_api_unavailable_when_the_request_cannot_connect(): void
    {
        Http::fake([
            'api.zippopotam.us/es/29001' => Http::failedConnection('Connection timed out'),
        ]);

        $result = $this->service->updatedPostalCode('29001');

        $this->assertSame([
            'available' => false,
            'cities' => [],
            'province' => '',
        ], $result);
    }
}
