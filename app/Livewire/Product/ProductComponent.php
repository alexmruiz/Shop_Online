<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Category;
use Livewire\Attributes\On;

#[Title('Productos')]
class ProductComponent extends Component
{
    use WithPagination; // Habilita la paginación en el componente
    protected $paginationTheme = 'bootstrap'; // Configura el tema de paginación a Bootstrap
   
    // Propiedades de la clase
    public $totalRegistros = 0;
    public $search = '';
    public $cant = 5;

    // Propiedades del modelo
    public $name;
    public $Id = 0;
    public $category_id;
    public $description;
    public $price;
    public $is_active = 1;

    // Renderiza la vista del componente
    public function render()
    {
        $this->totalRegistros = Product::count(); // Cuenta el total de registros de productos
        $products = Product::where('name', 'like', '%'.$this->search.'%')
            ->orderBy('id', 'desc') 
            ->paginate($this->cant); // Paginación de productos según la búsqueda
        return view('livewire.product.product-component', [
            'products' => $products
        ]);
    }

    #[Computed()]
    public function categories()
    {
        return Category::all(); // Obtiene todas las categorías
    }

    // Abre el modal para crear un nuevo producto
    public function create()
    {
        $this->Id = 0;
        $this->clean();
        $this->dispatch('open-modal', 'modalProduct');
    }

    // Almacena un nuevo producto en la base de datos
    public function store()
    {
        $rules = [
            'name' => 'required|min:5|max:255|unique:products',
            'description' => 'max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|numeric',
        ];
    
        $messages = [
            'name.required' => 'El nombre es requerido',
            'name.min' => 'El nombre debe tener al menos 5 caracteres.',
            'name.max' => 'El nombre no puede exceder los 255 caracteres.',
            'name.unique' => 'El nombre ya está registrado en las categorías.',
            'description.max' => 'La descripción no puede exceder los 255 caracteres.',
            'price.numeric' => 'El precio debe ser un número.',
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.numeric' => 'La categoría debe ser un número válido.',
        ];
    
        $this->validate($rules, $messages);

        $product = new Product();
        $product->name = $this->name;
        $product->description = $this->description;
        $product->price = $this->price;
        $product->category_id = $this->category_id;
        $product->is_active = $this->is_active;
        $product->save();

        $this->dispatch('close-modal', 'modalProduct');
        $this->dispatch('msg', 'Producto creado correctamente');
        $this->clean();
    }

    // Abre el modal para editar un producto existente
    public function edit(Product $product)
    {
        $this->clean();
        $this->Id = $product->id;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->is_active = $product->is_active;
        $this->category_id = $product->category_id;

        $this->dispatch('open-modal', 'modalProduct');
    }

    // Actualiza un producto existente en la base de datos
    public function update(Product $product)
    {
        $rules = [
            'name' => 'required|min:5|max:255|unique:products',
            'description' => 'max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|numeric',
        ];
    
        $messages = [
            'name.required' => 'El nombre es requerido',
            'name.min' => 'El nombre debe tener al menos 5 caracteres.',
            'name.max' => 'El nombre no puede exceder los 255 caracteres.',
            'name.unique' => 'El nombre ya está registrado en las categorías.',
            'description.max' => 'La descripción no puede exceder los 255 caracteres.',
            'price.numeric' => 'El precio de compra debe ser un número.',
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.numeric' => 'La categoría debe ser un número válido.',
        ];

        $this->validate($rules, $messages);

        $product->name = $this->name;
        $product->description = $this->description;
        $product->price = $this->price;
        $product->category_id = $this->category_id;
        $product->is_active = $this->is_active;
        $product->update();

        $this->dispatch('close-modal', 'modalProduct');
        $this->dispatch('msg', 'Producto editado correctamente');
        $this->clean();
    }

    // Método encargado de la limpieza del modal
    public function clean()
    {
        $this->reset(['Id', 'name', 'description', 'price', 'is_active', 'category_id']);
        $this->resetErrorBag();
    }

    //Elimina el producto
    #[On('destroyProduct')]
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        $this->dispatch('msg', 'El producto ha sido eliminado correctamente');
    }

    // Renderiza el botón para crear un nuevo producto
    public function renderCreateButton()
    {
        return '<a href="#" class="btn btn-primary" wire:click="create">
                <i class="fas fa-plus-circle"></i>
                Crear producto              
            </a> ';
    }
}
