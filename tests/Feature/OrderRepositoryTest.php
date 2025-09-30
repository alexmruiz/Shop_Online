<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\User;
use App\Repositories\OrderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_user_orders_returns_paginated_orders()
    {
        //1. Crear un usuario de prueba
        $user = User::factory()->create();

        //2. Crear pedidos asociados al usuario
        $cart1 = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'order_number' => 'ORD123456',
        ]);

        $cart2 = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'shipped',
            'order_number' => 'ORD654321',
        ]);

        //3. Llamada al repositorio
        $repo = new OrderRepository();

        //4. LLamar al método getUserOrders
        $result = $repo->getUserOrders($user);

        //5. Aserciones
        $this->assertCount(2, $result);
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
        $this->assertEquals('ORD654321', $result->first()->order_number);
    }

    /**
     * Test
     * @return void
     */
    public function test_get_user_orders_with_search()
    {
        $user = User::factory()->create();

        Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'order_number' => 'ABC123',
        ]);

        Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'order_number' => 'XYZ456',
        ]);

        $repo = new OrderRepository();

        // Buscar solo 'ABC'
        $result = $repo->getUserOrders($user, 'ABC');

        $this->assertCount(1, $result);
        $this->assertEquals('ABC123', $result->first()->order_number);
    }
}
