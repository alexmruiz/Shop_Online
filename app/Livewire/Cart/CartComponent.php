<?php

namespace App\Livewire\Cart;

use App\Enums\CartStockActions;
use App\Enums\Currency;
use App\Facades\Cart as CartFacade;
use App\Services\CurrencyConverterService;
use Illuminate\Support\Facades\Auth;
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
        CartFacade::updateStockProduct($itemId, CartStockActions::INCREMENT);
        $this->loadCart();
    }

    /**
     * Summary of decreaseQuantity
     * @param mixed $itemId
     * @return void
     */
    public function decreaseQuantity($itemId)
    {
        CartFacade::updateStockProduct($itemId, CartStockActions::DECREMENT);
        $this->loadCart();
    }

    /**
     * Summary of removeFromCart
     * @param mixed $itemId
     * @return void
     */
    public function removeFromCart($itemId)
    {
        CartFacade::updateStockProduct($itemId, CartStockActions::DELETE);
        $this->loadCart();
    }

    public function render(CurrencyConverterService $converter)
    {
        $currencyUser = Auth::check() ? Auth::user()->currency : Currency::EUR;

        foreach($this->cartItems as &$item) {
            $item['formatPrice'] = $converter->convertFromEur($currencyUser, $item['price']);
        }

        $this->total = $converter->convertFromEur($currencyUser, $this->total);

        return view('livewire.cart.cart-component', [
            'cartItems' => $this->cartItems,
            'total' => $this->total,
        ]);
    }
}
