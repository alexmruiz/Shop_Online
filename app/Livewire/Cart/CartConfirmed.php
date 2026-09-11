<?php

namespace App\Livewire\Cart;

use App\Enums\CartStatus;
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
            $cart = Auth::user()->carts()->find($cartId);
        } else {
            $cart = Auth::user()->carts()->where('status', CartStatus::PROCESSING)->latest()->first();
        }

        if (!$cart) {
            session()->flash('error', 'No se encontró ningún pedido confirmado.');
            return redirect()->route('home');
        }

        $this->cart = $cart;
        $service->cartStateManager($this->cart, '', true);
    }
    
    /**
     * Summary of generateInvoice
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function generateInvoice()
    {
        $cart = isset($this->cart)
            ? $this->cart
            : Auth::user()->carts()->findOrFail(request()->integer('cart_id'));

        return InvoiceFacade::generateInvoice($cart);
    }

    #[Layout('components.layouts.app_public')]
    public function render()
    {

        return view('livewire.cart.cart-confirmed');
    }
}
