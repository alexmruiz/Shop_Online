<?php

namespace App\Listeners;

use App\Enums\CartStatus;
use App\Models\Cart;
use App\Services\CheckoutService;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;

class StripeWebhookListener
{
    public function __construct(
        private readonly CheckoutService $checkoutService,
    ) {}

    public function handle(WebhookReceived $event): void
    {
        // $event->payload es el JSON completo que Stripe envió, ya decodificado como array
        $type = $event->payload['type'] ?? null;

        if ($type !== 'checkout.session.completed') {
            return; // ignoramos cualquier otro tipo de evento que Stripe nos mande
        }

        $session = $event->payload['data']['object'] ?? null;

        if (empty($session)) {
            Log::warning('Webhook de Stripe sin objeto de sesión', ['payload' => $event->payload]);
            return;
        }

        // Necesitas identificar a qué Cart pertenece esta sesión.
        // Cashier permite adjuntar metadata al crear la sesión de checkout (ver paso siguiente).
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

        $this->checkoutService->cartStateManager($cart, '', isAcepted: true);
    }
}