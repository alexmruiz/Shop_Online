<?php

namespace App\Services;

use App\Enums\CartStatus;
use App\Jobs\Notification;
use App\Models\User;
use App\Models\Cart;
use Exception;

class CheckoutService
{
    /**
     * Procesa el checkout para un usuario dado.
     * @param \App\Models\User $user
     * @param array $addressData
     */
    public function process(User $user, array $addressData, bool $saveStreet = false)
    {
        try {
            $cart = $this->getPendingCart($user);

            $address = $this->formatAddress($addressData);

            if(!empty($saveStreet)) {
                $user->update(['address' => $address]);
            }

            $this->cartStateManager($cart, $address);

            $amount = $this->calculateTotal($cart);

            return $this->createCheckoutSession($user, $cart, $amount);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Obtiene el carrito pendiente del usuario.
     * @param \App\Models\User $user
     * @throws \Exception
     * @return Cart|object
     */
    private function getPendingCart(User $user): Cart
    {
        $cart = $user->carts()->where('status', CartStatus::PENDING)->first();

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
     * Maneja el estado del carrito antes y despues del proceso de pago
     *
     * @param Cart $cart
     * @param string $address
     * @param boolean $isAcepted
     * @param boolean $isCancelled
     * @return void
     */
    public function cartStateManager(Cart $cart, string $address, bool $isAcepted = false, bool $isCancelled = false): void
    {
        if (!empty($isAcepted)) {
            $cart->update([
                'status' => CartStatus::CONFIRMED,
                'order_number' => $this->generateOrderNumber(),
            ]);
            Notification::dispatch($cart);
        } elseif (!empty($isCancelled)) {
            $cart->update(['status' => 'pending']);
        } else {
            // Solo guardar dirección, el estado se cambia en createCheckoutSession
            $cart->update(['address' => $address]);
        }
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
        // Cambiar estado a "processing" ANTES de crear la sesión
        $cart->update(['status' => CartStatus::PROCESSING]);

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
            'success_url' => route('confirmed', ['cart_id' => $cart->id]),
            'cancel_url' => route('checkout-cancel', ['cart_id' => $cart->id]),
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
