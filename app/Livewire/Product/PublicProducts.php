<?php

namespace App\Livewire\Product;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

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
    
        // Propiedades del modelo
        public $name;
        public $Id = 0;
        public $category_id;
        public $description;
        public $price;
        public $is_active = 1;
        public $selectedCategory = null;
    
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

    //#[On('addToCart')]
    public function addToCart($productId)
    {
        $product = Product::find($productId);
        
        $cart = session()->get('cart', []);

        if (isset($cart[$product['id']])) {
            $cart[$product['id']]['quantity'] += 1;
        } else {
            $cart[$product['id']] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => 1,
            ];
        }

        session()->put('cart', $cart);

        // Emite el evento correctamente
        //$this->emit('itemAdded'); // Esto ahora funciona correctamente

        // Actualiza el carrito
        $this->updateCart();
    }

    public function updateCart()
    {
        $this->cartItems = session()->get('cart', []);
        $this->total = collect($this->cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    

}
