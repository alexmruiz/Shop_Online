<?php

namespace Tests\Feature\Services;

use App\Enums\CartStatus;
use App\Exceptions\CheckoutService\CartNotFoundException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Services\CheckoutService;
use App\Services\StripePaymentService;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Checkout;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckoutServiceTest extends TestCase
{
    use RefreshDatabase;

    private StripePaymentService|Mockery\MockInterface $stripeMock;
    private UserService|Mockery\MockInterface $userServiceMock;
    private CheckoutService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear Mocks de las dependencias inyectadas en el constructor
        $this->stripeMock = Mockery::mock(StripePaymentService::class);
        $this->userServiceMock = Mockery::mock(UserService::class);

        // Instanciar el servicio con las dependencias mockeadas
        $this->service = new CheckoutService(
            $this->stripeMock,
            $this->userServiceMock
        );
    }

    #[Test]
    public function it_processes_checkout_successfully_without_saving_street(): void
    {
        // 1. Arrange: Datos de entrada y creación de modelos
        $user = User::factory()->create();
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => CartStatus::PENDING,
        ]);

        $product = Product::factory()->create(['price' => 5000]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'unit_price' => $product->price,
            'quantity' => 2, // Total = 10000
        ]);

        $addressData = [
            'street' => 'Calle Mayor 1',
            'city' => 'Madrid',
            'province' => 'Madrid',
            'postalCode' => '28001',
        ];

        $formattedAddress = 'Calle Mayor 1, Madrid, Madrid, 28001';

        // Definir expectativas de las dependencias
        $this->userServiceMock
            ->shouldReceive('formatAddress')
            ->once()
            ->with($addressData)
            ->andReturn($formattedAddress);

        // No debe llamarse a saveStreet porque $saveStreet es false por defecto
        $this->userServiceMock
            ->shouldReceive('saveStreet')
            ->never();

        $checkoutMock = Mockery::mock(Checkout::class);
        $this->stripeMock
            ->shouldReceive('createCheckoutSession')
            ->once()
            ->with($user, Mockery::type(Cart::class), 10000.0)
            ->andReturn($checkoutMock);

        // 2. Act
        $result = $this->service->process($user, $addressData);

        // 3. Assert
        $this->assertSame($checkoutMock, $result);

        // Verificar cambios en la BD
        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'status' => CartStatus::PROCESSING,
            'address' => $formattedAddress,
        ]);
    }

    #[Test]
    public function it_saves_street_when_save_street_flag_is_true(): void
    {
        // 1. Arrange
        $user = User::factory()->create();
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => CartStatus::PENDING,
        ]);

        $addressData = [
            'street' => 'Gran Vía 12',
            'city' => 'Madrid',
            'province' => 'Madrid',
            'postalCode' => '28013',
        ];

        // Se espera que guarde la dirección en el perfil del usuario
        $this->userServiceMock
            ->shouldReceive('saveStreet')
            ->once()
            ->with($user, $addressData);

        $this->userServiceMock
            ->shouldReceive('formatAddress')
            ->once()
            ->andReturn('Gran Vía 12, Madrid, Madrid, 28013');

        $this->stripeMock
            ->shouldReceive('createCheckoutSession')
            ->once()
            ->andReturn(Mockery::mock(Checkout::class));

        // 2. Act
        $this->service->process($user, $addressData, saveStreet: true);
    }

    #[Test]
    public function it_throws_exception_if_no_pending_cart_found(): void
    {
        // 1. Arrange: Usuario sin carrito pendiente
        $user = User::factory()->create();

        $addressData = [
            'street' => 'Calle Falsa 123',
            'city' => 'Barcelona',
            'province' => 'Barcelona',
            'postalCode' => '08001',
        ];

        // 3. Assert: Esperar la excepción
        $this->expectException(CartNotFoundException::class);
        $this->expectExceptionMessage('No se encontró un carrito asociado al usuario.');

        // 2. Act
        $this->service->process($user, $addressData);
    }

    #[Test]
    public function it_updates_cart_status_to_pending_when_cancelled(): void
    {
        // 1. Arrange
        $cart = Cart::factory()->create([
            'status' => CartStatus::PROCESSING,
        ]);

        // 2. Act
        $this->service->cancelled($cart);

        // 3. Assert
        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'status' => CartStatus::PENDING,
        ]);
    }

    #[Test]
    public function it_saves_address_in_cart(): void
    {
        // 1. Arrange
        $cart = Cart::factory()->create();
        $address = 'Avenida Diagonal 400, Barcelona, Barcelona, 08037';

        // 2. Act
        $this->service->saveAddressCart($cart, $address);

        // 3. Assert
        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'address' => $address,
        ]);
    }
}