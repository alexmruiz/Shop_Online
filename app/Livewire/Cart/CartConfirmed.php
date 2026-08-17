<?php

namespace App\Livewire\Cart;

use App\Facades\InvoiceFacade;
use App\Models\Cart;
use App\Services\CheckoutService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Este componente muestra la confirmación del pedido realizado por el usuario.
 * Busca el último carrito con status = processing del usuario autenticado y permite generar la factura asociada.
 */
#[Title('Pedido Confirmado')]
class CartConfirmed extends Component
{
    public Cart $cart;

    public function mount(CheckoutService $service)
    {
        // Buscar por ID del carrito desde la URL o el más reciente con status = processing
        $cartId = request('cart_id');
        
        if ($cartId) {
            $this->cart = Auth::user()->carts()->find($cartId);
        } else {
            $this->cart = Auth::user()->carts()->where('status', 'processing')->latest()->first();
        }

        if (!$this->cart) {
            session()->flash('error', 'No se encontró ningún pedido confirmado.');
            return redirect()->route('home');
        }

        $service->cartStateManager($this->cart, '', true);
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
