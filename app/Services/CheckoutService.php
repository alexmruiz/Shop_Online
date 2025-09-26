<?php

namespace App\Services;

use App\Models\User;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use Exception;

class CheckoutService
{
    /**
     * Procesa el checkout para un usuario dado.
     * @param \App\Models\User $user
     * @param array $addressData
     */
    public function process(User $user, array $addressData)
    {
        return DB::transaction(function () use ($user, $addressData) {
            $cart = $this->getPendingCart($user);

            $address = $this->formatAddress($addressData);

            $this->confirmCart($cart, $address);

            $amount = $this->calculateTotal($cart);

            return $this->createCheckoutSession($user, $cart, $amount);
        });
    }

    /**
     * Obtiene el carrito pendiente del usuario.
     * @param \App\Models\User $user
     * @throws \Exception
     * @return Cart|object
     */
    private function getPendingCart(User $user): Cart
    {
        $cart = $user->carts()->where('status', 'pending')->first();

        if (!$cart) {
            throw new Exception('No se encontró un carrito asociado al usuario.');
        }

        return $cart;
    }

    /**
     * Retorna la dirección formateada.
     * @param array $data
     * @return string
     */
    private function formatAddress(array $data): string
    {
        return "{$data['street']}, {$data['city']}, {$data['province']}, {$data['postalCode']}";
    }

    /**
     * Summary of confirmCart
     * @param \App\Models\Cart $cart
     * @param string $address
     * @return void
     */
    private function confirmCart(Cart $cart, string $address): void
    {
        $cart->update([
            'address' => $address,
            'status' => 'confirmed',
            'order_number' => $this->generateOrderNumber(),
        ]);
    }

    /**
     * Calcula el total del carrito.
     * @param \App\Models\Cart $cart
     * @return int
     */
    private function calculateTotal(Cart $cart): int
    {
        return $cart->cartItems->sum(fn($item) => $item->unit_price * $item->quantity);
    }

    /**
     * Crea una sesión de checkout con Stripe.
     * @param \App\Models\User $user
     * @param \App\Models\Cart $cart
     * @param int $amount
     * @return \Laravel\Cashier\Checkout
     */
    private function createCheckoutSession(User $user, Cart $cart, int $amount)
    {
        return $user->checkout([[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'Compra en mi tienda #' . $cart->id,
                ],
                'unit_amount' => $amount * 100,
            ],
            'quantity' => 1,
        ]], [
            'success_url' => route('confirmed'),
            'cancel_url' => route('checkout-cancel'),
        ]);
    }

    /**
     * Genera un número de orden único.
     * @return string
     */
    private function generateOrderNumber(): string
    {
        $date = now()->format('YmdHis');
        return $date . '-' . mt_rand(1000, 9999);
    }
}
