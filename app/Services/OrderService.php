<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\OrderRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderService
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Obtiene pedidos ya formateados/listos para mostrar en la vista.
     */
    public function getOrders(User $user, string $search = '', int $perPage = 5): LengthAwarePaginator
    {
        $carts = $this->orderRepository->getUserOrders($user, $search, $perPage);

        return $this->formatOrders($carts);
    }

    /**
     * Aplica cálculos y formato a los pedidos.
     */
    private function formatOrders($carts): LengthAwarePaginator
    {
        $carts->transform(function ($cart) {
            $cart->total = $cart->cartItems->sum(fn($item) => $item->quantity * $item->unit_price);
            $cart->formatted_date = $cart->created_at->format('d/m/Y');
            return $cart;
        });

        return $carts;
    }
}
