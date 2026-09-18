<?php

namespace Tests\Feature\Services;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserService();
    }

    #[Test]
    public function it_updates_user_address_in_database(): void
    {
        // 1. Arrange
        $user = User::factory()->create();

        $addressData = [
            'street' => 'Calle Gran Vía 28',
            'city' => 'Madrid',
            'province' => 'Madrid',
            'postalCode' => '28013',
        ];

        // 2. Act
        $this->service->saveStreet($user, $addressData);

        // 3. Assert
        $user->refresh();

        // Verificar que el modelo contiene la estructura actualizada
        $this->assertEquals([
            'street' => 'Calle Gran Vía 28',
            'city' => 'Madrid',
            'province' => 'Madrid',
            'postalCode' => '28013',
        ], $user->address);
    }

    #[Test]
    public function it_formats_address_array_into_a_single_string(): void
    {
        // 1. Arrange
        $addressData = [
            'street' => 'Avenida Diagonal 123',
            'city' => 'Barcelona',
            'province' => 'Barcelona',
            'postalCode' => '08008',
        ];

        $expectedFormat = 'Avenida Diagonal 123, Barcelona, Barcelona, 08008';

        // 2. Act
        $formattedAddress = $this->service->formatAddress($addressData);

        // 3. Assert
        $this->assertEquals($expectedFormat, $formattedAddress);
    }
}
