<?php

namespace App\Livewire\Product;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Category;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;

#[Title('Productos')]
class ProductComponent extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap'; // Configura el tema de paginación a Bootstrap

    // Propiedades de la clase
    public int $totalRegistros = 0;
    public string $search = '';
    public int $cant = 10;

    // Propiedades del modelo
    public string $name;
    public int $productId = 0;
    public int $categoryId;
    public string $description;
    public float $price;
    public int $stock;
    public int $isActive = 1;
    public $image;

    // Renderiza la vista del componente
    public function render()
    {
        $this->totalRegistros = Product::count(); // Cuenta el total de registros de productos

        $products = Product::where('name', 'like', '%' . $this->search . '%')
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
        $this->productId = 0;
        $this->clean();
        $this->dispatch('open-modal', 'modalProduct');
    }

    // Almacena un nuevo producto en la base de datos
    public function store()
    {
        $rules = [
            'name' => 'required|min:5|max:255|unique:products',
            'description' => 'max:255',
            'stock' => 'required|integer',
            'price' => 'required|numeric',
            'category_id' => 'required|numeric',
            'image' => 'image|max:1024|nullable',
        ];

        $this->validate($rules);

        $product = new Product();

        if ($this->image) {
            $customName = uniqid() . '.' . $this->image->extension();
            $path = $this->image->storeAs("images/products/$customName");
            $product->image = $path;
        }

        $product->name = $this->name;
        $product->description = $this->description;
        $product->price = $this->price;
        $product->stock = $this->stock;
        $product->category_id = $this->categoryId;
        $product->is_active = $this->isActive;
        $product->save();

        $this->dispatch('close-modal', 'modalProduct');
        $this->dispatch('msg', 'Producto creado correctamente');
        $this->clean();
    }

    // Abre el modal para editar un producto existente
    public function edit(Product $product)
    {
        $this->clean();
        $this->productId = $product->id;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->isActive = $product->is_active;
        $this->categoryId = $product->category_id;
        $this->image = $product->image;

        $this->dispatch('open-modal', 'modalProduct');
    }

    // Actualiza un producto existente en la base de datos
    public function update(Product $product)
    {
        $rules = [
            'name' => "required|min:5|max:255|unique:products,name,{$product->id}",
            'description' => 'max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|numeric',
            'image' => 'image|max:1024|nullable',
            'stock' => 'required|integer'
        ];

        $this->validate($rules);

        if ($this->image) {
            $customName = uniqid() . '.' . $this->image->extension();
            $path = $this->image->storeAs("images/products/$customName", 'public');
            $product->image = $path;
        }

        $product->update([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category_id' => $this->categoryId,
            'is_active' => $this->isActive,
            'stock' => $this->stock,
            'image' => $product->image,
        ]);

        $this->dispatch('close-modal', 'modalProduct');
        $this->dispatch('msg', 'Producto editado correctamente');
        $this->clean();
    }

    // Método encargado de la limpieza del modal
    public function clean()
    {
        $this->reset(['productId', 'name', 'description', 'price', 'isActive', 'categoryId', 'stock']);
        $this->resetErrorBag();
    }

    //Elimina el producto
    #[On('destroyProduct')]
    public function destroy(int $id): void
    {
        $product = Product::findOrFail($id);
        $product->delete();

        $this->dispatch('msg', 'El producto ha sido eliminado correctamente');
    }

}
