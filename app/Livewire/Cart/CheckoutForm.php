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

    protected $messages = [
        'street.required' => 'La calle es obligatoria.',
        'street.string' => 'La calle debe ser una cadena de texto.',
        'street.max' => 'La calle no debe exceder los 255 caracteres.',
        
        'city.required' => 'La ciudad es obligatoria.',
        'city.string' => 'La ciudad debe ser una cadena de texto.',
        'city.max' => 'La ciudad no debe exceder los 255 caracteres.',
        
        'postalCode.required' => 'El código postal es obligatorio.',
        'postalCode.digits' => 'El código postal solo puede contener 5 digitos.',
        'postalCode.max' => 'El código postal no debe exceder los 20 caracteres.',
        
        'province.required' => 'La provincia es obligatoria.',
        'province.string' => 'La provincia debe ser una cadena de texto.',
        'province.max' => 'La provincia no debe exceder los 255 caracteres.',
        
        'paymentMethod.required' => 'El método de pago es obligatorio.',
        
        'cardNumber.digits' => 'El número de la tarjeta debe tener 16 dígitos.',
        'cardNumber.required' => 'El número de tarjeta es obligatorio',
               
        'expiryDate.date_format' => 'La fecha de caducidad debe estar en el formato mm/aa.',
        'expiryDate.required' => 'La fecha de caducidad es obligatoria',
                
        'cvv.digits' => 'El CVV debe tener 3 dígitos.',
        'cvv.required'=> 'El cvv es obligatorio',
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
    
        $this->validate($rules);
    
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
