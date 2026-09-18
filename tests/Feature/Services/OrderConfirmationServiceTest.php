<?php

namespace Tests\Feature\Services;

use App\Enums\CartStatus;
use App\Exceptions\CheckoutService\StockReservationException;
use App\Jobs\GenerateInvoiceJob;
use App\Jobs\SendOrderConfirmationJob;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\OrderConfirmationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderConfirmationServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderConfirmationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OrderConfirmationService();
    }

    #[Test]
    public function it_confirms_order_decrements_stock_and_dispatches_jobs_successfully(): void
    {
        // 1. Arrange
        Bus::fake();

        $cart = Cart::factory()->create([
            'status' => CartStatus::PROCESSING,
            'order_number' => null,
        ]);

        $product = Product::factory()->create([
            'stock' => 10,
            'reserved_stock' => 2,
        ]);

        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'reserved_until' => now()->addMinutes(15),
        ]);

        // 2. Act
        $this->service->confirm($cart);

        // 3. Assert
        // Verificar actualización del carrito
        $cart->refresh();
        $this->assertEquals(CartStatus::CONFIRMED, $cart->status);
        $this->assertNotNull($cart->order_number);

        // Verificar ajuste del producto
        $product->refresh();
        $this->assertEquals(8, $product->stock);
        $this->assertEquals(0, $product->reserved_stock);

        // Verificar liberación del ítem del carrito
        $cartItem->refresh();
        $this->assertNull($cartItem->reserved_until);

        // Verificar el despacho de los Jobs tras el commit de la transacción
        Bus::assertDispatched(GenerateInvoiceJob::class, function ($job) use ($cart) {
            return $job->cart->id === $cart->id;
        });

        Bus::assertDispatched(SendOrderConfirmationJob::class, function ($job) use ($cart) {
            return $job->cart->id === $cart->id;
        });
    }

    #[Test]
    public function it_ignores_confirmation_if_cart_is_already_confirmed(): void
    {
        // 1. Arrange
        Bus::fake();
        Log::spy();

        $cart = Cart::factory()->create([
            'status' => CartStatus::CONFIRMED,
            'order_number' => '20260918-1234',
        ]);

        // 2. Act
        $this->service->confirm($cart);

        // 3. Assert
        Log::shouldHaveReceived('info')
            ->once()
            ->with("Carrito {$cart->id} ya estaba confirmado, ignorando confirmación duplicada");

        // Asegurar que no se despacharon Jobs
        Bus::assertNotDispatched(GenerateInvoiceJob::class);
        Bus::assertNotDispatched(SendOrderConfirmationJob::class);
    }

    #[Test]
    public function it_throws_exception_and_rolls_back_when_stock_is_insufficient(): void
    {
        // 1. Arrange
        Bus::fake();

        // Declarar explícitamente order_number como null
        $cart = Cart::factory()->create([
            'status' => CartStatus::PROCESSING,
            'order_number' => null,
        ]);

        // Producto con stock menor a la cantidad requerida
        $product = Product::factory()->create([
            'stock' => 1,
            'reserved_stock' => 1,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 5, // Requiere más del stock disponible (1)
        ]);

        // 3. Assert: Esperar la excepción
        $this->expectException(StockReservationException::class);
        $this->expectExceptionMessage('La reserva de stock no es válida.');

        // 2. Act
        try {
            $this->service->confirm($cart);
        } finally {
            // Verificar rollback de base de datos
            $cart->refresh();
            $this->assertEquals(CartStatus::PROCESSING, $cart->status);
            $this->assertNull($cart->order_number); // Ahora sí será null tras el rollback

            $product->refresh();
            $this->assertEquals(1, $product->stock);

            // Asegurar que no se despachó ningún Job
            Bus::assertNotDispatched(GenerateInvoiceJob::class);
            Bus::assertNotDispatched(SendOrderConfirmationJob::class);
        }
    }
}
