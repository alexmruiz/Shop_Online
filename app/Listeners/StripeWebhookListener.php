<?php

namespace App\Listeners;

use App\Enums\CartStatus;
use App\Models\Cart;
use App\Services\CheckoutService;
use App\Services\OrderConfirmationService;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;

class StripeWebhookListener
{
    public function __construct(
        private readonly CheckoutService $checkoutService,
        private OrderConfirmationService $orderConfirmation
    ) {}

    public function handle(WebhookReceived $event): void
    {
        $type = $event->payload['type'] ?? null;

        $session = $event->payload['data']['object'] ?? null;

        if (empty($session)) {
            Log::warning('Webhook de Stripe sin objeto de sesión', ['payload' => $event->payload]);
            return;
        }

        $cartId = $session['metadata']['cart_id'] ?? null;

        if (empty($cartId)) {
            Log::error('Webhook de Stripe sin cart_id en metadata', ['session_id' => $session['id'] ?? null]);
            return;
        }

        $cart = Cart::find($cartId);

        if (empty($cart)) {
            Log::error("Webhook de Stripe: carrito {$cartId} no encontrado");
            return;
        }


        // Idempotencia: si el carrito ya está confirmado, no lo proceses dos veces
        if ($cart->status === CartStatus::CONFIRMED) {
            Log::info("Webhook de Stripe: carrito {$cartId} ya estaba confirmado, evento ignorado");
            return;
        }

        $confirmingEvents = ['checkout.session.completed', 'checkout.session.async_payment_succeeded'];
        $cancellingEvents = ['checkout.session.expired', 'checkout.session.async_payment_failed'];

        if (in_array($type, $confirmingEvents, true)) {
            $this->orderConfirmation->confirm($cart);
        } elseif (in_array($type, $cancellingEvents, true)) {
            $this->checkoutService->cancelled($cart);
        }
    }
}
