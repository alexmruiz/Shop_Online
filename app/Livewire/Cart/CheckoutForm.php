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
    public $cardNumber;
    public $expiryDate;
    public $cvv;
    public $province;

    protected $rules = [
        'street' => ['required', 'string', 'max:255'],
        'city' => ['required', 'string', 'max:255'],
        'postalCode' => ['required', 'digits:5'],
        'province' => ['required', 'string', 'max:255'],
        'paymentMethod' => ['required'],
        'cardNumber' => ['digits:16'],
        'expiryDate' => ['date_format:m/y'],
        'cvv' => ['digits:3'],
    ];

 protected function messages()
    {
        return [
            'street.required' => __('checkout-validation.street_required'),
            'street.string' => __('checkout-validation.street_string'),
            'street.max' => __('checkout-validation.street_max'),

            'city.required' => __('checkout-validation.city_required'),
            'city.string' => __('checkout-validation.city_string'),
            'city.max' => __('checkout-validation.city_max'),

            'postalCode.required' => __('checkout-validation.postalCode_required'),
            'postalCode.digits' => __('checkout-validation.postalCode_digits'),

            'province.required' => __('checkout-validation.province_required'),
            'province.string' => __('checkout-validation.province_string'),
            'province.max' => __('checkout-validation.province_max'),

            'paymentMethod.required' => __('checkout-validation.paymentMethod_required'),

            'cardNumber.required' => __('checkout-validation.cardNumber_required'),
            'cardNumber.digits' => __('checkout-validation.cardNumber_digits'),

            'expiryDate.required' => __('checkout-validation.expiryDate_required'),
            'expiryDate.date_format' => __('checkout-validation.expiryDate_date_format'),

            'cvv.required' => __('checkout-validation.cvv_required'),
            'cvv.digits' => __('checkout-validation.cvv_digits'),
        ];
    }


    public function mount()
    {
        $this->dispatch('update-breadcrumbs', [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Cesta de la compra', 'url' => route('cart')],
            ['name' => 'Checkout', 'url' => null],
        ]);
    }

    public function processPayment()
    {
        // Define reglas dinámicas basadas en el método de pago seleccionado
        $rules = $this->rules;

        if ($this->paymentMethod === 'card') {
            $rules['cardNumber'] = ['required', 'digits:16'];
            $rules['expiryDate'] = ['required', 'date_format:m/y'];
            $rules['cvv'] = ['required', 'digits:3'];
        } else {
            // Remueve las reglas para los campos de tarjeta si no es método 'card'
            unset($rules['cardNumber'], $rules['expiryDate'], $rules['cvv']);
        }

        $this->validate($rules, $this->messages());

        // Obtiene el carrito del usuario
        $cart = Auth::user()->carts()->where('status', 'pending')->first();

        if (!$cart) {
            session()->flash('error', 'No se encontró un carrito asociado al usuario.');
            return redirect()->route('home');
        }

        $address = "{$this->street}, {$this->city}, {$this->province}, {$this->postalCode}";

        if ($this->paymentMethod === 'paypal') {
            session()->flash('success', 'Tu pago ha sido completado con PayPal.');
        } else {
            session()->flash('success', 'Tu pago ha sido procesado exitosamente con tarjeta.');
        }

        // Actualiza el estado del carrito
        $updated = $cart->update([
            'address' => $address,
            'status' => 'confirmed',
            'order_number' => $this->generateOrderNumber(),
        ]);

        if (!$updated) {
            session()->flash('error', 'No se pudo actualizar.');
            return redirect()->back();
        }

        return redirect()->route('confirmed');
    }

    private function generateOrderNumber()
    {
        $date = now()->format('YmdHis'); // Obtiene la fecha y hora actual en formato: YYYYMMDDHHMMSS
        return $date . '-' . mt_rand(1000, 9999); // Añade un número aleatorio para hacer el valor único
    }

    public function render()
    {
        return view('livewire.cart.checkout-form');
    }
}
