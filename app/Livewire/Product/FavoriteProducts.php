<?php

namespace App\Livewire\Product;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Home')]
class FavoriteProducts extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
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
    public function render()
    {
        $products =  Auth::user()->favoriteProducts()->latest('favorite_products.created_at')->paginate(12);
        foreach ($products as &$product) {
            $total = $product->stock - $product->reserved_stock;
            $product['available_stock'] = $total;
        }

        return view('livewire.product.favorite-products', [
            'products' => $products,
        ]);
    }
}
