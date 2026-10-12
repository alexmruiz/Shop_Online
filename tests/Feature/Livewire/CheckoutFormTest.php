<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Cart\CheckoutForm;
use App\Models\User;
use App\Services\PostalCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckoutFormTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_city_options_and_province_when_postal_code_is_found(): void
    {
        $user = User::factory()->create([
            'address' => [
                'street' => 'Calle Mayor 1',
                'city' => 'Old City',
                'postalCode' => '28000',
                'province' => 'Old Province',
            ],
        ]);

        $postalCodeService = Mockery::mock(PostalCodeService::class);
        $postalCodeService
            ->shouldReceive('updatedPostalCode')
            ->once()
            ->with('28001')
            ->andReturn([
                'cities' => [
                    'Madrid' => 'Madrid',
                    'Alcobendas' => 'Alcobendas',
                ],
                'province' => 'Madrid',
                'available' => true,
            ]);

        $this->app->instance(PostalCodeService::class, $postalCodeService);

        Livewire::actingAs($user)
            ->test(CheckoutForm::class)
            ->set('postalCode', '28001')
            ->assertSet('city', '')
            ->assertSet('cityOptions', [
                'Madrid' => 'Madrid',
                'Alcobendas' => 'Alcobendas',
            ])
            ->assertSet('province', 'Madrid')
            ->assertSet('apiAvailable', true)
            ->assertHasNoErrors();
    }

    #[Test]
    public function it_adds_a_validation_error_when_postal_code_is_not_found(): void
    {
        $user = User::factory()->create();

        $postalCodeService = Mockery::mock(PostalCodeService::class);
        $postalCodeService
            ->shouldReceive('updatedPostalCode')
            ->once()
            ->with('00000')
            ->andReturn([
                'cities' => [],
                'province' => '',
                'available' => true,
                'found' => false,
            ]);

        $this->app->instance(PostalCodeService::class, $postalCodeService);

        Livewire::actingAs($user)
            ->test(CheckoutForm::class)
            ->set('postalCode', '00000')
            ->assertSet('city', '')
            ->assertSet('cityOptions', [])
            ->assertSet('province', '')
            ->assertHasErrors('postalCode')
            ->assertSee(__('checkout.postal_code_not_found'));
    }

    #[Test]
    public function it_enables_manual_address_entry_when_the_api_is_unavailable(): void
    {
        $user = User::factory()->create();

        $postalCodeService = Mockery::mock(PostalCodeService::class);
        $postalCodeService
            ->shouldReceive('updatedPostalCode')
            ->once()
            ->with('28001')
            ->andReturn([
                'available' => false,
                'cities' => [],
                'province' => '',
            ]);

        $this->app->instance(PostalCodeService::class, $postalCodeService);

        Livewire::actingAs($user)
            ->test(CheckoutForm::class)
            ->set('postalCode', '28001')
            ->assertSet('city', '')
            ->assertSet('cityOptions', [])
            ->assertSet('province', '')
            ->assertSet('apiAvailable', false)
            ->assertHasNoErrors();
    }
}
