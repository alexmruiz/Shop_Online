<?php

namespace App\Livewire\Orders;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Mis pedidos')]
#[Layout('components.layouts.app_public')]
class MyOrders extends Component
{
    public $carts;

    public function mount()
    {
        $this->carts = Auth::user()->carts()->with('cartItems.product')->get()->map(function ($cart) {
            // Calcula el total
            $cart->total = $cart->cartItems->sum(function ($item) {
                return $item->quantity * $item->unit_price;
            });
            // Formatea la fecha
            $cart->formatted_date = $cart->created_at->format('d/m/Y');
            return $cart;
        })->filter(function ($cart) {
            // Filtra los carritos que no tengan el estado 'PENDING'
            return $cart->status !== 'pending';
        });
    }

    public function render()
    {
        return view('livewire.orders.my-orders', [
            'carts' => $this->carts,
        ]);
        
    }   
}
