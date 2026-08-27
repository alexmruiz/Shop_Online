<?php

namespace Tests\Feature;

use App\Enums\CartStatus;
use App\Enums\CartStockActions;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    private CartService $cartService;
    private User $user;
    private Cart $cart;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartService = new CartService();
        $this->user = User::factory()->create();

        // Autenticar al usuario para el contexto de la prueba
        $this->actingAs($this->user);

        // Crear carrito inicial en estado pendiente
        $this->cart = Cart::factory()->create([
            'user_id' => $this->user->id,
            'status' => CartStatus::PENDING,
        ]);
    }

    /** @test */
    public function it_creates_or_gets_pending_cart()
    {
        // Crear un usuario de prueba
        $user = User::factory()->create();

        // Simular que el usuario está logueado
        $this->actingAs($user);

        // Primera llamada: debería crear un carrito pendiente
        $cart = $this->cartService->getOrCreatePendingCart();

        // Comprobamos que se ha creado correctamente
        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertEquals(CartStatus::PENDING, $cart->status);

        // Refrescamos el usuario para que cargue las relaciones desde la DB
        $user->refresh();

        // Segunda llamada: debería devolver el mismo carrito
        $cart2 = $this->cartService->getOrCreatePendingCart();

        // Comprobamos que no se ha creado un nuevo carrito
        $this->assertEquals($cart->id, $cart2->id);
    }


    /** @test */
    public function it_adds_product_to_cart()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create(['price' => 100, 'stock' => 20]);

        $this->cartService->addToCart($product->id);
        $cart = $this->cartService->getOrCreatePendingCart();

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 100
        ]);

        // Añadir de nuevo debe incrementar cantidad
        $this->cartService->addToCart($product->id);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    /** @test */
    public function it_loads_cart_data_correctly()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product1 = Product::factory()->create(['price' => 100]);
        $product2 = Product::factory()->create(['price' => 50]);

        $this->cartService->addToCart($product1->id);
        $this->cartService->addToCart($product2->id);

        $cartData = $this->cartService->loadCart();

        $this->assertCount(2, $cartData['items']);
        $this->assertEquals(150, $cartData['total']);
    }

    /** @test */
    public function it_decrements_cart_item_quantity_and_increments_product_stock()
    {
        $product = Product::factory()->create(['stock' => 5]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $this->cart->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $this->cartService->updateStockProduct($cartItem->id, CartStockActions::DECREMENT);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 6,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 2,
        ]);
    }

    /** @test */
    public function it_removes_cart_item_when_decrementing_quantity_of_one()
    {
        $product = Product::factory()->create(['stock' => 5]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $this->cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->cartService->updateStockProduct($cartItem->id, CartStockActions::DECREMENT);

        // El stock sube a 6
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 6,
        ]);

        // El ítem ya no existe en la base de datos
        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id,
        ]);
    }

    /** @test */
    public function it_deletes_cart_item_and_restores_full_stock()
    {
        $product = Product::factory()->create(['stock' => 10]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $this->cart->id,
            'product_id' => $product->id,
            'quantity' => 4,
        ]);

        $this->cartService->updateStockProduct($cartItem->id, CartStockActions::DELETE);

        // Se reponen las 4 unidades al stock original (10 + 4 = 14)
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 14,
        ]);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id,
        ]);
    }
}
