<?php

namespace Tests\Feature\Repository;

use App\Enums\CartStatus;
use App\Models\Cart;
use App\Models\User;
use App\Repositories\OrderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected OrderRepository $repo;

    /**
     * Configura el entorno de prueba.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new OrderRepository();
    }

    #[Test]
    public function it_returns_user_orders_paginated(): void
    {
        // 1. Crear usuario de prueba
        $user = User::factory()->create();

        // 2. Crear pedidos asociados al usuario
        Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'order_number' => 'ORD123456',
        ]);

        Cart::factory()->create([
            'user_id' => $user->id,
            'status' => CartStatus::CONFIRMED,
            'order_number' => 'ORD654321',
        ]);

        // 3. Obtener resultados del repositorio
        $result = $this->repo->getUserOrders($user);

        // 4. Aserciones
        $this->assertCount(2, $result);
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals('ORD654321', $result->first()->order_number);
    }

    #[Test]
    public function it_filters_user_orders_by_search_term(): void
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

        // Buscar solo 'ABC'
        $result = $this->repo->getUserOrders($user, 'ABC');

        $this->assertCount(1, $result);
        $this->assertEquals('ABC123', $result->first()->order_number);
    }
}