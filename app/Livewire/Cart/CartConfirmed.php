<?php

namespace App\Livewire\Cart;

use App\Facades\InvoiceFacade;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Este componente muestra la confirmación del pedido realizado por el usuario.
 * Busca el último carrito confirmado del usuario autenticado y permite generar la factura asociada.
 */
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
    
    /**
     * Summary of generateInvoice
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
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
