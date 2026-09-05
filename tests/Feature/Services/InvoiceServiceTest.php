<?php

namespace Tests\Feature\Services;

use App\Enums\CartStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InvoiceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected InvoiceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new InvoiceService();
    }

    #[Test]
    public function it_generates_pdf_for_confirmed_cart()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear carrito confirmado con un producto
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => CartStatus::CONFIRMED,
            'order_number' => '12345',
            'address' => 'Calle de prueba 1',
        ]);

        $product = Product::factory()->create(['name' => 'Producto 1', 'price' => 100]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => $product->price,
        ]);

        $response = $this->service->generateInvoice($cart);

        $this->assertEquals(200, $response->status());
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('factura_12345.pdf', $response->headers->get('Content-Disposition'));
        $this->assertNotEmpty($response->getContent());
    }

    #[Test]
    public function test_user_gets_forbidden_when_downloading_other_user_invoice(): void
    {
        $ownerUser = User::factory()->create();
        $otherUser = User::factory()->create();

        $cart = Cart::factory()->create([
            'user_id' => $ownerUser->id,
            'status' => CartStatus::CONFIRMED,
        ]);

        // Intentar acceder a la ruta autenticado como el usuario no autorizado
        $response = $this->actingAs($otherUser)
            ->get(route('download.invoice', $cart->id));

        // Afirmar que devuelve status HTTP 403 Forbidden
        $response->assertForbidden();
    }
}
