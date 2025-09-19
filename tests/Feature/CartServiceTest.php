<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CartService();
    }

    /** @test */
    /** @test */
    public function it_creates_or_gets_pending_cart()
    {
        // Crear un usuario de prueba
        $user = User::factory()->create();

        // Simular que el usuario está logueado
        $this->actingAs($user);

        // Primera llamada: debería crear un carrito pendiente
        $cart = $this->service->getOrCreatePendingCart();

        // Comprobamos que se ha creado correctamente
        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertEquals('pending', $cart->status);

        // Refrescamos el usuario para que cargue las relaciones desde la DB
        $user->refresh();

        // Segunda llamada: debería devolver el mismo carrito
        $cart2 = $this->service->getOrCreatePendingCart();

        // Comprobamos que no se ha creado un nuevo carrito
        $this->assertEquals($cart->id, $cart2->id);
    }


    /** @test */
    public function it_adds_product_to_cart()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create(['price' => 100]);

        $this->service->addToCart($product->id);
        $cart = $this->service->getOrCreatePendingCart();

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 100
        ]);

        // Añadir de nuevo debe incrementar cantidad
        $this->service->addToCart($product->id);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    /** @test */
    public function it_increases_and_decreases_quantity()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create(['price' => 50]);
        $this->service->addToCart($product->id);
        $cart = $this->service->getOrCreatePendingCart();
        $item = $cart->cartItems()->first();

        $this->service->increaseQuantity($item->id);
        $this->assertEquals(2, $item->fresh()->quantity);

        $this->service->decreaseQuantity($item->id);
        $this->assertEquals(1, $item->fresh()->quantity);

        // Decrementa hasta eliminar
        $this->service->decreaseQuantity($item->id);
        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }

    /** @test */
    public function it_removes_item_from_cart()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();
        $this->service->addToCart($product->id);
        $cart = $this->service->getOrCreatePendingCart();
        $item = $cart->cartItems()->first();

        $this->service->removeFromCart($item->id);
        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }

    /** @test */
    public function it_loads_cart_data_correctly()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product1 = Product::factory()->create(['price' => 100]);
        $product2 = Product::factory()->create(['price' => 50]);

        $this->service->addToCart($product1->id);
        $this->service->addToCart($product2->id);

        $cartData = $this->service->loadCart();

        $this->assertCount(2, $cartData['items']);
        $this->assertEquals(150, $cartData['total']);
    }
}
