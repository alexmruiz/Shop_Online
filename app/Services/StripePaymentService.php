<?php

namespace App\Services;

use App\Enums\CartStatus;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class StripePaymentService
{
    /**
     * Crea una sesión de checkout con Stripe.
     * @param \App\Models\User $user
     * @param \App\Models\Cart $cart
     * @param float $amount Importe total en euros.
     * @return \Laravel\Cashier\Checkout
     */
    public function createCheckoutSession(User $user, Cart $cart, float $amount)
    {
        if ($cart->status === CartStatus::PROCESSING) {
            Log::info('Checkout iniciado', [
                'user_id' => $user->id,
                'cart_id' => $cart->id,
                'total' => $amount,
            ]);

            return $user->checkout([[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Compra en mi tienda #' . $cart->id,
                    ],
                    'unit_amount' => (int) round($amount * 100),
                ],
                'quantity' => 1,
            ]], [
                'success_url' => route('confirmed', ['cart_id' => $cart->id]),
                'cancel_url' => route('checkout-cancel', ['cart_id' => $cart->id]),
                'metadata' => [
                    'cart_id' => $cart->id
                ]
            ]);
        }
    }
}
