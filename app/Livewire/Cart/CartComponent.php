<?php

namespace App\Livewire\Cart;

use App\Facades\Cart as CartFacade;
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
        $this->loadCart(); // Carga los datos del carrito cuando se renderiza el componente
        $this->dispatch('update-breadcrumbs', [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Cesta de la compra', 'url' => null],
        ]);
    }

    //Carga el carrito asociado al usuario
    public function loadCart()
    {
        $cartData = CartFacade::loadCart();
        $this->cartItems = $cartData['items'];
        $this->total = $cartData['total'];
    }

    //Incrementa una unidad del producto seleccionado
    public function increaseQuantity($itemId)
    {
        CartFacade::increaseQuantity($itemId);
        $this->loadCart();
    }

    //Decrementa una unidad del producto seleccionado
    public function decreaseQuantity($itemId)
    {
        CartFacade::decreaseQuantity($itemId);
        $this->loadCart();
    }

    //Elimina el producto seleccionado
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
