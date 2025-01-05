<?php

namespace App\Livewire\Cart;


use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Checkout')]
class CheckoutForm extends Component
{
    #[Layout('components.layouts.app_public')]

    public $street;
    public $city;
    public $postalCode;
    public $address;
    public $paymentMethod;
    public $cardNumber;
    public $expiryDate;
    public $cvv;
    public $province;

    // protected $rules = [
    //     'address' => ['required', 'string', 'max:255'],
    //     'paymentMethod' => ['required', 'in:paypal,card'],
    //     'cardNumber' => ['required_if:paymentMethod,card', 'digits:16'],
    //     'expiryDate' => ['required_if:paymentMethod,card', 'date_format:m/y'],
    //     'cvv' => ['required_if:paymentMethod,card', 'digits:3'],
    // ];
    

    public function processPayment()
    {
        
        //$this->validate();

        //Otiene el carrito del usuario
        $cart = Auth::user()->carts()->where('status', 'pending')->first();

        if (!$cart) {
            session()->flash('error', 'No se encontró un carrito asociado al usuario.');
            return redirect()->route('home');
        }


        $address = "{$this->street}, {$this->city}, {$this->province}, {$this->postalCode}";

        //dd($cart);
        if($this->paymentMethod === 'paypal') {
            session()->flash('success','Tu pago ha sido completado con paypal');
        }else{
            session()->flash('success','Tu pago ha sido procesado exitosamente con tarjeta');
        }
        
        //Actualiza el estado del carrito
        $updated = $cart->update([
            'address' => $address,
            'status' => 'confirmed',
            'order_number' => $this->generateOrderNumber(),
        ]);

        if(!$updated){
            session()->flash('error', 'No se pudo actualizar');
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
