<?php

namespace App\Livewire\Cart;

use App\Services\CheckoutService;
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
    public $province;
    public $paymentMethod;

    protected $rules = [
        'street' => ['required', 'string', 'max:255'],
        'city' => ['required', 'string', 'max:255'],
        'postalCode' => ['required', 'digits:5'],
        'province' => ['required', 'string', 'max:255'],
    ];

    public function processPayment(CheckoutService $checkoutService)
    {
        $this->validate();

        try {
            return $checkoutService->process(Auth::user(), [
                'street' => $this->street,
                'city' => $this->city,
                'province' => $this->province,
                'postalCode' => $this->postalCode,
            ]);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->route('home');
        }
    }

    public function render()
    {
        return view('livewire.cart.checkout-form');
    }
}
