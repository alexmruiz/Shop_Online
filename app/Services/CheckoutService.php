<?php

namespace App\Services;

use App\Enums\CartStatus;
use App\Exceptions\CheckoutService\CartNotFoundException;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Support\Facades\Log;

class CheckoutService
{
    public function __construct(
        private StripePaymentService $stripe,
        private UserService $userService
    ) {}

    /**
     * Procesa el checkout para un usuario dado.
     * @param \App\Models\User $user
     * @param array $addressData
     */
    public function process(User $user, array $addressData, bool $saveStreet = false)
    {
        try {
            $cart = $this->getPendingCart($user);

            if (!empty($saveStreet)) {
                $this->userService->saveStreet($user, $addressData);
            }

            $address = $this->userService->formatAddress($addressData);

            $this->saveAddressCart($cart, $address);

            $cart->update(['status' => CartStatus::PROCESSING]);

            $amount = $this->calculateTotal($cart);

            return $this->stripe->createCheckoutSession($user, $cart, $amount);
        } catch (\Throwable $th) {
            Log::error("Error en: " . __METHOD__, [
                'user_id' => $user->id ?? null,
                'cart_id' => $cart->id ?? null,
                'exception' => $th->getMessage(),
            ]);
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
            throw new CartNotFoundException('No se encontró un carrito asociado al usuario.');
        }

        return $cart;
    }


    public function cancelled(Cart $cart)
    {
        $cart->update(['status' => CartStatus::PENDING]);
    }

    public function saveAddressCart(Cart $cart, string $address)
    {
        $cart->update(['address' => $address]);
    }

    /**
     * Calcula el total del carrito.
     * @param \App\Models\Cart $cart
     * @return float
     */
    private function calculateTotal(Cart $cart): float
    {
        return
            $cart->cartItems->sum(fn($item) => $item->unit_price * $item->quantity);
    }
}
