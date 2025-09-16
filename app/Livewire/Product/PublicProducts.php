<?php

namespace App\Livewire\Product;

use App\Facades\Cart as CartFacade;
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

    protected $cartService;


    #[Computed()] // Obtiene todas las categorías
    public function categories()
    {
        return Category::all(); 
    }

    public function mount()
    {
        $this->dispatch('update-breadcrumbs', [
            ['name' => 'Home', 'url' => null],
        ]);
        
    }
    

    public function getOrCreatePendingCart()
    {
        return CartFacade::getOrCreatePendingCart(); //Retornar el carrito
    }

    /**
     * Agrega un producto al carrito y actualiza la vista del carrito
     * @param mixed $productId
     * @return void
     */
    public function addToCart($productId)
    {
        CartFacade::addToCart($productId); // Utiliza la fachada para agregar al carrito
        //$this->feedbackMessage = 'Producto añadido al carrito correctamente.';
        $this->updateCart(); // Actualizar la vista del carrito
        $this->dispatch('product-add', id: $productId); 
    }

    public function updateCart()
    {
        // Carga los datos actuales del carrito utilizando la fachada
        $cartData = CartFacade::loadCart(); 
        $this->cartItems = $cartData['items'];
        $this->total = $cartData['total'];
    }

    #[Layout('components.layouts.app_public')]
    public function render()
    {
        $query = Product::query();

        // Recupera productos aplicando filtros de búsqueda y categoría.
        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        // Busca un producto por su nombre
        if ($this->search) {
            $query->where('name', 'like', '%'.$this->search.'%');
        }

        // Los productos son ordenados de forma descendente
        $products = $query->orderBy('id', 'desc')->paginate($this->cant);

        // Obtener el carrito del usuario o crear uno si no existe
        $cart = Auth::user() ? $this->getOrCreatePendingCart() : null;

        if ($cart) {
            $this->countItems = $cart->cartItems()->count();
            $this->total = $cart->cartItems()->sum('quantity'); 
        }

        return view('livewire.product.public-products', [
            'products' => $products,
            'categories' => Category::all(),
        ]);
    }
}
