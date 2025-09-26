<?php

namespace App\Livewire\Product;

use App\Facades\Cart as CartFacade;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Services\CartService;
use App\Services\ProductService;
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

    private ?CartService $cartService = null;
    private ?ProductService $productService = null;


    #[Computed()] // Obtiene todas las categorías
    public function categories()
    {
        return Category::all();
    }

    public function mount(ProductService $productService, CartService $cartService)
    {
        $this->productService = $productService;
        $this->cartService = $cartService;

        $this->dispatch('update-breadcrumbs', [
            ['name' => 'Home', 'url' => null],
        ]);

        if (Product::count() === 0) {
            $this->productService->importProducts();
        }
    }

    /**
     * Obtiene o crea un carrito pendiente para el usuario autenticado.
     * @return \App\Models\Cart|null
     */
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

    /**
     * Summary of updateCart
     * @return void
     */
    public function updateCart()
    {
        // Carga los datos actuales del carrito utilizando la fachada
        $cartData = CartFacade::loadCart();
        $this->cartItems = $cartData['items'];
        $this->total = $cartData['total'];
    }

    #[Layout('components.layouts.app_public')]
    public function render(ProductRepository $repository, CartService $cartService)
    {
        if(!$this->cartService) {
            $this->cartService = $cartService;
        }
        
        $products = $repository->searchAndFilter(
            $this->search,
            $this->selectedCategory,
            $this->cant
        );

        $cart = Auth::user() ? $this->cartService->getOrCreatePendingCart() : null;

        if ($cart) {
            $this->countItems = $cart->cartItems()->count();
            $this->total = $cart->cartItems()->sum('quantity');
        }

        return view('livewire.product.public-products', [
            'products' => $products,
            'categories' => $repository->getAllCategories(),
        ]);
    }
}
