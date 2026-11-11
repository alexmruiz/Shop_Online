<?php

namespace App\Livewire\Cart;

use App\Enums\CartStatus;
use App\Enums\Currency;
use App\Facades\InvoiceFacade;
use App\Models\Cart;
use App\Services\CurrencyConverterService;
use App\Services\CurrencyFormatService;
use App\Services\OrderConfirmationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
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

    public string $currencyUser = Currency::EUR->value;

    public function mount(OrderConfirmationService $service)
    {
        // Buscar por ID del carrito desde la URL o el más reciente con status = processing
        $cartId = request('cart_id');

        if ($cartId) {
            $cart = Auth::user()->carts()->find($cartId);
        } else {
            $cart = Auth::user()->carts()->where('status', CartStatus::PROCESSING)->latest()->first();
        }

        if (! $cart) {
            session()->flash('error', 'No se encontró ningún pedido confirmado.');

            return redirect()->route('home');
        }

        $this->cart = $cart;
        $this->currencyUser = Auth::user()?->currency?->value ?? Currency::EUR->value;

        if ($cart->status === CartStatus::PROCESSING) {
            $service->confirm($cart);
        }
    }

    /**
     * Summary of generateInvoice
     *
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
    public function render(
        CurrencyConverterService $converter,
        CurrencyFormatService $format
    ) {
        $currency = Currency::from($this->currencyUser);
        $cartItemsFormat = $this->cart->cartItems()
            ->with('product')
            ->get()
            ->each(function ($item) use ($currency, $converter, $format): void {
                $amount = $item->unit_price * $item->quantity;
                $convertedAmount = $currency === Currency::EUR
                    ? (string) $amount
                    : $converter->convertFromEur($currency, (string) $amount);

                $item->formatPrice = $format->formatMoney($currency, $convertedAmount);
            });

        $total = $this->cart->cartItems->sum(
            fn ($item) => $item->quantity * $item->unit_price
        );
        $totalConverted = $currency === Currency::EUR
            ? (string) $total
            : $converter->convertFromEur($currency, (string) $total);

        return view('livewire.cart.cart-confirmed', [
            'cartItemsFormat' => $cartItemsFormat,
            'totalFormat' => $format->formatMoney($currency, $totalConverted),
        ]);
    }

    #[On('currencyuser-updated')]
    public function listenCurrencyUser(string $currency): void
    {
        $this->currencyUser = Currency::from($currency)->value;
    }
}
