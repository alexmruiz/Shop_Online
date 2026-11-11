<?php

namespace App\Livewire\Cart;

use App\Enums\CartStockActions;
use App\Enums\Currency;
use App\Facades\Cart as CartFacade;
use App\Services\CurrencyConverterService;
use App\Services\CurrencyFormatService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Componente Livewire para gestionar la cesta de la compra.
 * Permite ver los productos en el carrito, modificar cantidades y eliminar productos.
 */
#[Title("Cesta de la compra")]
class CartComponent extends Component
{
    public array $cartItems = [];
    public int $total = 0;
    public string $currencyUser = Currency::EUR->value;

    #[Layout('components.layouts.app_public')]
    public function mount()
    {
        $this->loadCart(); // Carga los datos del carrito cuando se renderiza el componente
        $this->dispatch('update-breadcrumbs', [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Cesta de la compra', 'url' => null],
        ]);

        $this->currencyUser = Auth::user()?->currency->value ?? Currency::EUR->value;

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

    public function transformCash(array $cartItemsFormat, Currency $currencyUser, CurrencyFormatService $format, CurrencyConverterService $converter): array
    {
        foreach($cartItemsFormat as &$item) {
            if ($currencyUser !== Currency::EUR) {
                $priceFormat = $converter->convertFromEur($currencyUser, $item['price']);

                $item['formatPrice'] = $format->formatMoney($currencyUser, $priceFormat);
            }else {
                $item['formatPrice'] = $format->formatMoney($currencyUser, $item['price']);
            }
        }

        return $cartItemsFormat;
    }

    public function render(CurrencyConverterService $converter, CurrencyFormatService $format)
    {
        $cartItemsFormat = $this->cartItems;

        $currencyUserToEnum = Currency::from($this->currencyUser);

        $cartItemsFormat = $this->transformCash($cartItemsFormat, $currencyUserToEnum, $format, $converter);

        $totalConvert = $currencyUserToEnum !== Currency::EUR ? $converter->convertFromEur($currencyUserToEnum, $this->total) : $this->total;
        
        $totalFormat = $format->formatMoney($currencyUserToEnum, $totalConvert);

        return view('livewire.cart.cart-component', [
            'cartItemsFormat' => $cartItemsFormat,
            'totalFormat' => $totalFormat,
        ]);
    }

    #[On('currencyuser-updated')]
    public function listenCurrencyUser(string $currency)
    {
        $this->currencyUser = Currency::from($currency)->value;
    }
}
