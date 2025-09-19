<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new InvoiceService();
    }

    /** @test */
    public function test_generate_invoice_redirects_to_home_if_no_cart()
    {
        // 1. Crear un usuario ficticio
        $user = User::factory()->create();

        // 2. Simular que está logueado
        $this->actingAs($user);

        // 3. Ejecutar el método del servicio
        $service = new \App\Services\InvoiceService();
        $response = $service->generateInvoice();

        // 4. Afirmar que redirige a 'home'
        $this->assertEquals(route('home'), $response->headers->get('Location'));
    }

    /** @test */
    public function it_generates_pdf_for_confirmed_cart()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear carrito confirmado con un producto
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'confirmed',
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

        $response = $this->service->generateInvoice();

        $this->assertEquals(200, $response->status());
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('factura-12345.pdf', $response->headers->get('Content-Disposition'));
        $this->assertNotEmpty($response->getContent());
    }
}
