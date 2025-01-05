<?php

namespace App\Livewire\Cart;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
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
    }

    public function loadCart()
    {
        $cart = Auth::user()->carts()->where('status', 'pending')->first();
    
        if ($cart) {
            // Carga los elementos del carrito
            $this->cartItems = $cart->cartItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->product->name,
                    'product_id' => $item->product->id,
                    'quantity' => $item->quantity,
                    'price' => $item->unit_price,
                ];
            })->toArray();
    
            // Calcula el total
            $this->total = $cart->cartItems->sum(function ($item) {
                return $item->quantity * $item->unit_price;
            });
        } else {
            // Si no hay un carrito "pending", crear uno
            $cart = Cart::create([
                'user_id' => Auth::id(),
                'status' => 'pending',
                'order_number' => $this->generateOrderNumber(),
            ]);
    
            $this->cartItems = [];
            $this->total = 0;
        }
    }
    

    public function increaseQuantity($itemId)
    {
        $cart = Auth::user()->carts()->where('status', 'pending')->first();
        if ($cart) {
            $item = $cart->cartItems()->find($itemId);
            if ($item) {
                $item->increment('quantity');
                $this->loadCart();
            }
        }
    }
    

    public function decreaseQuantity($itemId)
    {
        $cart = Auth::user()->carts()->where('status', 'pending')->first();
    
        if ($cart) {
            // Busca el item en el carrito
            $item = $cart->cartItems()->find($itemId);
    
            if ($item) {
                if ($item->quantity > 1) {
                    // Disminuir la cantidad si es mayor a 1
                    $item->decrement('quantity');
                } elseif ($item->quantity === 1) {
                    // Eliminar del carrito si la cantidad es 1
                    $this->removeFromCart($itemId);
                }
                // Recargar el carrito
                $this->loadCart();
            } else {
                // Opcional: Manejar el caso de un item no encontrado
                session()->flash('error', 'El item no se encontró en el carrito.');
            }
        } else {
            // Opcional: Manejar el caso de un carrito no encontrado
            session()->flash('error', 'No se encontró un carrito asociado al usuario.');
        }
    }
    
    

    public function removeFromCart($itemId)
    {
        $cart = Auth::user()->carts()->where('status', 'pending')->first();
    
        if ($cart) {
            $item = $cart->cartItems()->find($itemId);
            if ($item) {
                $item->delete();
                $this->loadCart();
            }
        }
    }
    

    public function render()
    {
        return view('livewire.cart.cart-component', [
            'cartItems' => $this->cartItems,
            'total' => $this->total,
        ]);
    }
}
