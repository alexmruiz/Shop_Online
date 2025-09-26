<?php

namespace App\Livewire\Orders;

use App\Facades\InvoiceFacade;
use App\Services\OrderService;
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

    private OrderService $orderService;

    public function boot(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function mount()
    {
        $this->dispatch('update-breadcrumbs', [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Mis pedidos', 'url' => null],
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function downloadInvoice($id)
    {
        return InvoiceFacade::downloadInvoice($id);
    }

    public function render()
    {
        $orders = $this->orderService->getOrders(Auth::user(), $this->search, $this->cant);

        return view('livewire.orders.my-orders', [
            'carts' => $orders,
        ]);
    }
}
