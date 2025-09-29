<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Notification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $cart;
    /**
     * Create a new job instance.
     */
    public function __construct($cart)
    {
        $this->cart = $cart;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $cart = \App\Models\Cart::with('user')->find($this->cart->id);

            if (!$cart || !$cart->user) {
                Log::error("Carrito o usuario no encontrado para enviar confirmación.");
                return;
            }

            Mail::to($cart->user->email)->send(new \App\Mail\OrderConfirmedMail($cart));
        } catch (\Exception $e) {
            Log::error('Error al enviar el correo de confirmación: ' . $e->getMessage());
        }
    }
}
