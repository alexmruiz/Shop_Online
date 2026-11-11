<?php

namespace App\Livewire\Product;

use App\Enums\Currency;
use App\Services\CurrencyConverterService;
use App\Services\CurrencyFormatService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Home')]
class FavoriteProducts extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $currencyUser = Currency::EUR->value;

    public function mount()
    {
        $this->currencyUser = Auth::user()?->currency?->value ?? Currency::EUR->value;
        $this->dispatch('update-breadcrumbs', [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Favoritos', 'url' => null],
        ]);
    }

    public function toggleFavorite(int $productId): void
    {
        Auth::user()->favoriteProducts()->toggle($productId);
    }

    #[Layout('components.layouts.app_public')]
    public function render(
        CurrencyConverterService $converter,
        CurrencyFormatService $format
    ) {
        $products = Auth::user()
            ->favoriteProducts()
            ->latest('favorite_products.created_at')
            ->paginate(12);

        $currency = Currency::from($this->currencyUser);

        $products->getCollection()->transform(function ($product) use ($currency, $converter, $format) {
            $product->available_stock = $product->stock - $product->reserved_stock;

            $amount = $currency === Currency::EUR
                ? (string) $product->price
                : $converter->convertFromEur($currency, (string) $product->price);

            $product->formatPrice = $format->formatMoney($currency, $amount);

            return $product;
        });

        return view('livewire.product.favorite-products', [
            'products' => $products,
        ]);
    }

    #[On('currencyuser-updated')]
    public function listenCurrencyUser(string $currency)
    {
        $this->currencyUser = Currency::from($currency)->value;
    }
}
