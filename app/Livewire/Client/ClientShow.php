<?php

namespace App\Livewire\Client;

use App\Facades\InvoiceFacade;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Ver Usuario')]
class ClientShow extends Component
{
    public $user;

    public function mount(User $user)
    {
        // Cargar el usuario con los pedidos y sus items asociados
        $this->user = $user->load('carts.cartItems.product');
    }

    public function downloadInvoice($id)
    {
        return InvoiceFacade::downloadInvoice($id);
    }

    public function render()
    {
        return view('livewire.client.client-show');
    }
}
