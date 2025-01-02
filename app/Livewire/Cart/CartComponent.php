<?php

namespace App\Livewire\Cart;

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title("Cesta de la compra")]
class CartComponent extends Component
{
    public $cartItems = [];
    public $total = 0;

    public function mount()
    {
        $this->updateCart();
    }

    #[Layout('components.layouts.app_public')]
    public function render()
    {
        
        return view('livewire.cart.cart-component');
    }
  
    public function removeFromCart($itemId)
    {
        //Elimina un artículo de la cesta basado en su Id
        $cart = session()->get('cart', []);
        unset($cart[$itemId]);
        session()->put('cart', $cart);

        $this->updateCart();
    }
    public function checkout()
    {
        
    }

    public function updateCart()
    {
        $this->cartItems = session()->get('cart', []);
        $this->total = collect($this->cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);
    }



}
