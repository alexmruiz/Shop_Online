<?php

namespace App\Livewire\Orders;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Mis pedidos')]
#[Layout('components.layouts.app_public')]
class MyOrders extends Component
{
    use WithPagination;
    public $search = '';
    public $cant = 5;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        // Elimina $this->carts para que se maneje todo en el render
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Auth::user()->carts()->with('cartItems.product');

        if ($this->search) {
            $query->where('order_number', 'like', '%' . $this->search . '%');
        }

        // Pagina los resultados
        $carts = $query->where('status', '!=', 'pending')
                       ->orderBy('id', 'desc')
                       ->paginate($this->cant);
        
        // Formatea y calcula los valores que necesitas
        $carts->transform(function ($cart) {
            $cart->total = $cart->cartItems->sum(function ($item) {
                return $item->quantity * $item->unit_price;
            });
            $cart->formatted_date = $cart->created_at->format('d/m/Y');
            return $cart;
        });

        return view('livewire.orders.my-orders', [
            'carts' => $carts,
        ]);
    }
}
