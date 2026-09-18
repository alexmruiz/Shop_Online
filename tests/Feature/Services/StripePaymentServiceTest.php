<?php

namespace Tests\Feature\Services;

use App\Enums\CartStatus;
use App\Models\Cart;
use App\Models\User;
use App\Services\StripePaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Checkout;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StripePaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private StripePaymentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new StripePaymentService();
    }

    #[Test]
    public function it_creates_checkout_session_successfully_when_cart_is_in_processing_status(): void
    {
        // 1. Arrange
        Log::spy(); // Monitorear las llamadas al sistema de logs

        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 1;

        $cart = Cart::factory()->make([
            'id' => 99,
            'status' => CartStatus::PROCESSING,
        ]);

        $amount = 49.99; // Total en euros
        $expectedAmountInCents = 4999; // Convertido a céntimos

        $expectedLineItems = [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'Compra en mi tienda #99',
                ],
                'unit_amount' => $expectedAmountInCents,
            ],
            'quantity' => 1,
        ]];

        $expectedOptions = [
            'success_url' => route('confirmed', ['cart_id' => 99]),
            'cancel_url' => route('checkout-cancel', ['cart_id' => 99]),
            'metadata' => [
                'cart_id' => 99,
            ],
        ];

        $checkoutMock = Mockery::mock(Checkout::class);

        // Se espera que el usuario llame a ->checkout() con las estructuras exactas
        $user->shouldReceive('checkout')
            ->once()
            ->with($expectedLineItems, $expectedOptions)
            ->andReturn($checkoutMock);

        // 2. Act
        $result = $this->service->createCheckoutSession($user, $cart, $amount);

        // 3. Assert
        $this->assertSame($checkoutMock, $result);

        Log::shouldHaveReceived('info')
            ->once()
            ->with('Checkout iniciado', [
                'user_id' => $user->id,
                'cart_id' => $cart->id,
                'total' => $amount,
            ]);
    }

    #[Test]
    public function it_does_not_create_checkout_session_if_cart_is_not_processing(): void
    {
        // 1. Arrange
        Log::spy();

        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 1;

        // Carrito en estado PENDING (no PROCESSING)
        $cart = Cart::factory()->make([
            'id' => 50,
            'status' => CartStatus::PENDING,
        ]);

        // El usuario NUNCA debe llamar a checkout()
        $user->shouldReceive('checkout')->never();

        // 2. Act
        $result = $this->service->createCheckoutSession($user, $cart, 100.0);

        // 3. Assert
        $this->assertNull($result);

        // Comprobar correctamente que el log NO se invocó
        Log::shouldNotHaveReceived('info');
    }
}
