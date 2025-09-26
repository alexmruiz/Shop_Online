<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository
{
    /**
     * Obtiene los pedidos de un usuario autenticado (sin procesar).
     */
    public function getUserOrders(User $user, string $search = '', int $perPage = 5): LengthAwarePaginator
    {
        $query = $user->carts()->with('cartItems.product');

        if (!empty($search)) {
            $query->where('order_number', 'like', '%' . $search . '%');
        }

        return $query->where('status', '!=', 'pending')
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }
}
