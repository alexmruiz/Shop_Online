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

    // Renderiza la vista del componente
    #[Layout('components.layouts.app_public')]
    public function render()
    {
        $query = Product::query();
    
        // Aplicar filtro de categoría si está seleccionado
        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }
    
        // Aplicar filtro de búsqueda
        if ($this->search) {
            $query->where('name', 'like', '%'.$this->search.'%');
        }
    
        // Obtener productos paginados
        $products = $query->orderBy('id', 'desc')->paginate($this->cant);
    
        // Obtener el carrito del usuario autenticado
        $user = Auth::user();
        if ($user && $user->cart) {
            $cartId = $user->cart->id;
    
            // Contar productos distintos
            $distinctProducts = CartItem::where('cart_id', $cartId)->count();
    
            // Contar total de unidades
            $totalUnits = CartItem::where('cart_id', $cartId)->sum('quantity');
    
            // Actualizar los contadores
            $this->countItems = $distinctProducts; // Productos distintos
            $this->total = $totalUnits;           // Total de unidades
        } else {
            $this->countItems = 0;
            $this->total = 0;
        }
    
        return view('livewire.product.public-products', [
            'products' => $products,
            'categories' => Category::all(),
        ]);

    }
    
    #[Computed()]
    public function categories()
    {
        return Category::all(); 
    }

    public function addToCart($productId)
    {
        
        $product = Product::find($productId);
       
        $user = Auth::user();

        session()->flash('success', 'Prueba de mensaje');

        
        // Generar el número de orden único
        $orderNumber = $this->generateOrderNumber();
    
        // Si el carrito no existe lo crea con un order_number
        $cart = $user->cart ?? Cart::create([
            'user_id' => $user->id,
            'order_number' => $orderNumber, // Asignar el número de orden
        ]);
    
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

    }
    
    private function generateOrderNumber()
    {
        // Generar un prefijo con la fecha y hora actual
        $date = now()->format('YmdHis'); // Obtiene la fecha y hora actual en formato: YYYYMMDDHHMMSS
        return $date . '-' . mt_rand(1000, 9999); // Añadir un número aleatorio para hacer el valor único
    }
    

    public function updateCart()
    {
        $cart = Auth::user()->cart;

        if ($cart) {
            $this->cartItems = $cart->cartItems->map(fn($item) => [
                'id' => $item->product_id,
                'name' => $item->product->name,
                'price' => $item->unit_price,
                'quantity' => $item->quantity,
            ])->toArray();

            $this->total = collect($this->cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);
        } else {
            $this->cartItems = [];
            $this->total = 0;
        }
    }

}
