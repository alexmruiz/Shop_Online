<?php

namespace App\Livewire\Cart;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title("Cesta de la compra")]
class CartComponent extends Component
{
    public $cartItems = [];
    public $total = 0;

    #[Layout('components.layouts.app_public')]
    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $cart = Auth::user()->cart;
        if ($cart) {
            $this->cartItems = $cart->cartItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->product->name,
                    'product_id' => $item->product->id,
                    'quantity' => $item->quantity,
                    'price' => $item->unit_price,
                ];
            })->toArray();

            $this->total = $cart->cartItems->sum(function ($item) {
                return $item->quantity * $item->unit_price;
            });
        }
    }

    public function increaseQuantity($itemId)
    {
        $item = Auth::user()->cart->cartItems()->find($itemId);
        if ($item) {
            $item->increment('quantity');
            $this->loadCart();
        }
    }

    public function decreaseQuantity($itemId)
    {   
        $item = Auth::user()->cart->cartItems()->find($itemId);
        if ($item && $item->quantity > 1) {
            $item->decrement('quantity');
            $this->loadCart();
        }
    }

    public function removeFromCart($itemId)
    {       
        $item = Auth::user()->cart->cartItems()->find($itemId);
        if ($item) {
            $item->delete();
            $this->loadCart();
        }
    }

    public function checkout()
    {
        // Lógica para el pago
        session()->flash('success', 'Gracias por tu compra.');
        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.cart.cart-component', [
            'cartItems' => $this->cartItems,
            'total' => $this->total,
        ]);
    }
    
}
