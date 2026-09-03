<?php

namespace Tests\Feature\Services;

use App\Enums\CartStatus;
use App\Enums\CartStockActions;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    private CartService $cartService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartService = new CartService();
    }

    #[Test]
    public function it_creates_or_gets_pending_cart(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $cart = $this->cartService->getOrCreatePendingCart();

        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertEquals(CartStatus::PENDING, $cart->status);

        $user->refresh();

        $cart2 = $this->cartService->getOrCreatePendingCart();
        $this->assertEquals($cart->id, $cart2->id);
    }

    #[Test]
    public function it_adds_product_to_cart(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create(['price' => 100, 'stock' => 20, 'reserved_stock' => 0]);

        $this->cartService->addToCart($product->id);
        $cart = $this->cartService->getOrCreatePendingCart();

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 100
        ]);

        $this->cartService->addToCart($product->id);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        // Verificar que el stock reservado aumentó
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'reserved_stock' => 2,
        ]);
    }

    #[Test]
    public function it_loads_cart_data_correctly(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product1 = Product::factory()->create(['price' => 100, 'stock' => 10]);
        $product2 = Product::factory()->create(['price' => 50, 'stock' => 10]);

        $this->cartService->addToCart($product1->id);
        $this->cartService->addToCart($product2->id);

        $cartData = $this->cartService->loadCart();

        $this->assertCount(2, $cartData['items']);
        $this->assertEquals(150, $cartData['total']);
    }

    #[Test]
    public function it_decrements_cart_item_quantity_and_decrements_reserved_stock(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $cart = Cart::factory()->create(['user_id' => $user->id, 'status' => CartStatus::PENDING]);
        // Definimos stock = 5 y reserved_stock = 3 (sincronizado con las 3 unidades del CartItem)
        $product = Product::factory()->create(['stock' => 5, 'reserved_stock' => 3]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $this->cartService->updateStockProduct($cartItem->id, CartStockActions::DECREMENT);

        // El stock permanece intacto (5), pero el stock reservado baja a 2
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 5,
            'reserved_stock' => 2,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 2,
        ]);
    }

    #[Test]
    public function it_removes_cart_item_when_decrementing_quantity_of_one(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $cart = Cart::factory()->create(['user_id' => $user->id, 'status' => CartStatus::PENDING]);
        // Sincronizamos reserved_stock = 1
        $product = Product::factory()->create(['stock' => 5, 'reserved_stock' => 1]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->cartService->updateStockProduct($cartItem->id, CartStockActions::DECREMENT);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 5,
            'reserved_stock' => 0,
        ]);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id,
        ]);
    }

    #[Test]
    public function it_deletes_cart_item_and_releases_reserved_stock(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $cart = Cart::factory()->create(['user_id' => $user->id, 'status' => CartStatus::PENDING]);
        // Sincronizamos reserved_stock = 4
        $product = Product::factory()->create(['stock' => 10, 'reserved_stock' => 4]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 4,
        ]);

        $this->cartService->updateStockProduct($cartItem->id, CartStockActions::DELETE);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 10,
            'reserved_stock' => 0,
        ]);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id,
        ]);
    }
}