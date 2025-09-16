<?php

namespace App\Livewire\Cart;

use App\Facades\InvoiceFacade;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Pedido Confirmado')]
class CartConfirmed extends Component
{
    public $cart;

    public function mount()
    {
        //Busca los pedidos asociados al usuario con status = confirmed
        $this->cart = Auth::user()->carts()->where('status', 'confirmed')->latest()->first();
        if (!$this->cart) {
            session()->flash('error', 'No se encontró un pedido confirmado.');
            return redirect()->route('home');
        }
    }
    //Genera la factura asociada al pedido
    public function generateInvoice()
    {
        return InvoiceFacade::generateInvoice();
    }


    #[Layout('components.layouts.app_public')]
    public function render()
    {

        return view('livewire.cart.cart-confirmed');
    }
}
