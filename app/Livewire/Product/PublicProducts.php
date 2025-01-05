<?php

namespace App\Livewire\Product;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth; 
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Home')]
class PublicProducts extends Component
{
    use WithPagination; 
    protected $paginationTheme = 'bootstrap'; 

    // Propiedades de la clase
    public $search = '';
    public $cant = 15;
    public $cartItems = [];
    public $total = 0;
    public $selectedCategory = null;
    public $countItems = 0;
    public $feedbackMessage = null;

    #[Computed()]
    public function categories()
    {
        return Category::all(); 
    }

    private function getOrCreatePendingCart()
    {
        
        $user = Auth::user();

        if($user){
             // Obtener el carrito con estado "pending" o crear uno nuevo
        $cart = Auth::user()->carts()->where('status', 'pending')->first();

        if (!$cart) {
            $cart = Cart::create([
                'user_id' => $user->id,
                'status' => 'pending',
            ]);
        }

        return $cart;
        }
       
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        $cart = $this->getOrCreatePendingCart();

        $cartItem = $cart->cartItems()->where('product_id', $productId)->first();
        
        if ($cartItem) {
            $cartItem->increment('quantity');
        } else {
            $cart->cartItems()->create([
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price' => $product->price,
            ]);
        }

        $this->feedbackMessage = 'Producto añadido al carrito correctamente.';
        $this->updateCart(); // Actualizar la vista del carrito
    }

    public function updateCart()
    {
        $cart = $this->getOrCreatePendingCart();

        $this->cartItems = $cart->cartItems->map(fn($item) => [
            'id' => $item->product_id,
            'name' => $item->product->name,
            'price' => $item->unit_price,
            'quantity' => $item->quantity,
        ])->toArray();

        $this->total = collect($this->cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    #[Layout('components.layouts.app_public')]
    public function render()
    {
        $query = Product::query();
        
        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }
        
        if ($this->search) {
            $query->where('name', 'like', '%'.$this->search.'%');
        }

        $products = $query->orderBy('id', 'desc')->paginate($this->cant);
        $user = Auth::user();
        if($user){
            $cart = $this->getOrCreatePendingCart();
            $this->countItems = $cart->cartItems()->count();
            $this->total = $cart->cartItems()->sum('quantity');
        }
       

        return view('livewire.product.public-products', [
            'products' => $products,
            'categories' => Category::all(),
        ]);
    }
}
