<?php

namespace App\Livewire\Product;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Computed;
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
    
        // Propiedades del modelo
        public $name;
        public $Id = 0;
        public $category_id;
        public $description;
        public $price;
        public $is_active = 1;
        public $selectedCategory = null;
    
    // Renderiza la vista del componente
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

}
