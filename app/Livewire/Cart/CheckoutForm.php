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
        'postalCode' => ['required', 'string', 'max:20'],
        'province' => ['required', 'string', 'max:255'],
        'paymentMethod' => ['required'],
        'cardNumber' => ['required_if:paymentMethod,card', 'digits:16'],
        'expiryDate' => ['required_if:paymentMethod,card', 'date_format:m/y'],
        'cvv' => ['required_if:paymentMethod,card', 'digits:3'],
    ];

    protected $messages = [
        'street.required' => 'La calle es obligatoria.',
        'street.string' => 'La calle debe ser una cadena de texto.',
        'street.max' => 'La calle no debe exceder los 255 caracteres.',
        
        'city.required' => 'La ciudad es obligatoria.',
        'city.string' => 'La ciudad debe ser una cadena de texto.',
        'city.max' => 'La ciudad no debe exceder los 255 caracteres.',
        
        'postalCode.required' => 'El código postal es obligatorio.',
        'postalCode.string' => 'El código postal debe ser una cadena de texto.',
        'postalCode.max' => 'El código postal no debe exceder los 20 caracteres.',
        
        'province.required' => 'La provincia es obligatoria.',
        'province.string' => 'La provincia debe ser una cadena de texto.',
        'province.max' => 'La provincia no debe exceder los 255 caracteres.',
        
        'paymentMethod.required' => 'El método de pago es obligatorio.',
        
        'cardNumber.required_if' => 'El número de la tarjeta es obligatorio cuando el método de pago es tarjeta.',
        'cardNumber.digits' => 'El número de la tarjeta debe tener 16 dígitos.',
        
        'expiryDate.required_if' => 'La fecha de caducidad es obligatoria cuando el método de pago es tarjeta.',
        'expiryDate.date_format' => 'La fecha de caducidad debe estar en el formato mm/aa.',
        
        'cvv.required_if' => 'El CVV es obligatorio cuando el método de pago es tarjeta.',
        'cvv.digits' => 'El CVV debe tener 3 dígitos.',
    ];
    

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
        $this->validate();

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
