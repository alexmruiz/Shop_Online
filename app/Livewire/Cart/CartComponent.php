<?php

namespace App\Livewire\Cart;

use App\Facades\Cart as CartFacade;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Componente Livewire para gestionar la cesta de la compra.
 * Permite ver los productos en el carrito, modificar cantidades y eliminar productos.
 */
#[Title("Cesta de la compra")]
class CartComponent extends Component
{
    public $cartItems = [];
    public $total = 0;

    #[Layout('components.layouts.app_public')]
    public function mount()
    {
        $this->loadCart(); // Carga los datos del carrito cuando se renderiza el componente
        $this->dispatch('update-breadcrumbs', [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Cesta de la compra', 'url' => null],
        ]);
    }

    /**
     * Summary of loadCart
     * @return void
     */
    public function loadCart()
    {
        $cartData = CartFacade::loadCart();
        $this->cartItems = $cartData['items'];
        $this->total = $cartData['total'];
    }

    /**
     * Summary of increaseQuantity
     * @param mixed $itemId
     * @return void
     */
    public function increaseQuantity($itemId)
    {
        CartFacade::increaseQuantity($itemId);
        $this->loadCart();
    }

    /**
     * Summary of decreaseQuantity
     * @param mixed $itemId
     * @return void
     */
    public function decreaseQuantity($itemId)
    {
        CartFacade::decreaseQuantity($itemId);
        $this->loadCart();
    }

    /**
     * Summary of removeFromCart
     * @param mixed $itemId
     * @return void
     */
    public function removeFromCart($itemId)
    {
        CartFacade::removeFromCart($itemId);
        $this->loadCart();
    }

    public function render()
    {
        return view('livewire.cart.cart-component', [
            'cartItems' => $this->cartItems,
            'total' => $this->total,
        ]);
    }
}
