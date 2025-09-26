<?php

namespace App\Livewire\Cart;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Checkout')]
#[Layout('components.layouts.app_public')]
class CheckoutForm extends Component
{
    public $street;
    public $city;
    public $postalCode;
    public $address;
    public $paymentMethod;
    public $province;

    protected $rules = [
        'street' => ['required', 'string', 'max:255'],
        'city' => ['required', 'string', 'max:255'],
        'postalCode' => ['required', 'digits:5'],
        'province' => ['required', 'string', 'max:255'],
    ];

    public function processPayment()
    {
        $this->validate();

        $user = Auth::user();
        $cart = $user->carts()->where('status', 'pending')->first();

        if (!$cart) {
            session()->flash('error', 'No se encontró un carrito asociado al usuario.');
            return redirect()->route('home');
        }

        // Guardamos la dirección en el carrito
        $address = "{$this->street}, {$this->city}, {$this->province}, {$this->postalCode}";

        $updated = $cart->update([
            'address' => $address,
            'status' => 'confirmed',
            'order_number' => $this->generateOrderNumber(),
        ]);

        if (!$updated) {
            session()->flash('error', 'No se pudo actualizar el carrito.');
            return redirect()->back();
        }

        // Calculamos el total (en euros, luego pasamos a céntimos)
        $amount = $cart->cartItems->sum(function ($item) {
            return $item->unit_price * $item->quantity;
        });

        // Creamos sesión de checkout en Stripe
        return $user->checkout([
            [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Compra en mi tienda #' . $cart->id,
                    ],
                    'unit_amount' => $amount * 100, // céntimos
                ],
                'quantity' => 1,
            ]
        ], [
            'success_url' => route('confirmed'),
            'cancel_url' => route('checkout-cancel'),
        ]);
    }

    private function generateOrderNumber()
    {
        $date = now()->format('YmdHis'); // YYYYMMDDHHMMSS
        return $date . '-' . mt_rand(1000, 9999); // Número aleatorio para unicidad
    }

    public function render()
    {
        return view('livewire.cart.checkout-form');
    }
}
