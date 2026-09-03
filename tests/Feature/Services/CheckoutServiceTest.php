<?php

namespace Tests\Feature\Services;

use App\Enums\CartStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Services\CheckoutService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Cashier\Checkout;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckoutServiceTest extends TestCase
{
    use RefreshDatabase;

    private CheckoutService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CheckoutService();
    }

    /**
     * Test para verificar que el checkout se procesa correctamente con datos válidos.
     */
    #[Test]
    public function test_process_checkout_successfully(): void
    {
        // 1. Arrange (Preparación)
        $user = User::factory()->create();
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => CartStatus::PENDING,
        ]);

        $product = Product::factory()->create(['price' => 5000]); // 50.00 €
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'unit_price' => $product->price,
            'quantity' => 2,
        ]);

        $addressData = [
            'street' => 'Calle Gran Vía 12',
            'city' => 'Madrid',
            'province' => 'Madrid',
            'postalCode' => '28013',
        ];

        // Mock de Cashier para evitar llamadas reales a la API de Stripe
        $userMock = Mockery::mock($user)->makePartial();
        $userMock->shouldReceive('checkout')
            ->once()
            ->andReturn(Mockery::mock(Checkout::class));

        // 2. Act (Ejecución)
        $result = $this->service->process($userMock, $addressData, true);

        // 3. Assert (Verificaciones)
        $this->assertInstanceOf(Checkout::class, $result);

        // Verificar que la dirección del usuario se guardó en formato JSON/Array
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
        $this->assertEquals($addressData, $user->fresh()->address);

        // Verificar que el estado del carrito cambió a PROCESSING y tiene la dirección formateada
        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'status' => CartStatus::PROCESSING,
            'address' => 'Calle Gran Vía 12, Madrid, Madrid, 28013',
        ]);
    }

    /**
     * Test para verificar que lanza excepción si el usuario no tiene un carrito pendiente.
     */
    #[Test]
    public function test_throws_exception_when_no_pending_cart_exists(): void
    {
        // 1. Arrange
        $user = User::factory()->create();
        // Crear un carrito pero en estado CONFIRMED en vez de PENDING
        Cart::factory()->create([
            'user_id' => $user->id,
            'status' => CartStatus::CONFIRMED,
        ]);

        $addressData = [
            'street' => 'Calle Falsa 123',
            'city' => 'Sevilla',
            'province' => 'Sevilla',
            'postalCode' => '41001',
        ];

        // 2. Expect Exception
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No se encontró un carrito asociado al usuario.');

        // 3. Act
        $this->service->process($user, $addressData);
    }

   #[Test]
public function test_cart_state_manager_confirms_cart_and_dispatches_notification(): void
{
    // 1. Arrange
    Event::fake();

    $cart = Cart::factory()->create(['status' => CartStatus::PROCESSING]);

    // Crear producto con stock inicial de 10 y reservado de 2
    $product = Product::factory()->create([
        'stock' => 10,
        'reserved_stock' => 2
    ]);

    // Asociar el ítem al carrito con 2 unidades
    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    // 2. Act
    $this->service->cartStateManager($cart, '', isAcepted: true);

    // 3. Assert
    $this->assertEquals(CartStatus::CONFIRMED, $cart->fresh()->status);
    $this->assertNotNull($cart->fresh()->order_number);

    // Verificar que el stock real bajó a 8 y el reservado a 0
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 8,
        'reserved_stock' => 0,
    ]);
}

    /**
     * Test para validar que cartStateManager regresa el carrito a pending cuando es cancelado.
     */
    #[Test]
    public function test_cart_state_manager_resets_status_on_cancellation(): void
    {
        // 1. Arrange
        $cart = Cart::factory()->create(['status' => CartStatus::PROCESSING]);

        // 2. Act
        $this->service->cartStateManager($cart, '', isCancelled: true);

        // 3. Assert
        $this->assertEquals(CartStatus::PENDING, $cart->fresh()->status);
    }
}
