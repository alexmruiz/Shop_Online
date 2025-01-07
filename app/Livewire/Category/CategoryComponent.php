<?php

namespace App\Livewire\Category;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\Category;
use Livewire\Attributes\On;

/**
 * Componente Livewire para gestionar las categorías.
 * 
 * Este componente permite listar, crear, editar y eliminar categorías.
 * Integra paginación y búsqueda, y utiliza eventos Livewire para interactuar
 * con el frontend.
 */

#[Title('Categorias')]
class CategoryComponent extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    //Propiedades de la clase
    public $totalRegistros=0;
    public $search='';
    public $cant=5;
    //Propiedades modelo
    public $name = '';
    public $Id;
    
    public function render()
    {

        //$this->dispatch('open-modal', 'modalProduct');
        // Filtra las categorías por el nombre y realiza la paginación
        $this->totalRegistros = Category::count();
        $categories = Category::where('name', 'like', '%'.$this->search.'%')
                        ->orderBy('id', 'desc') 
                        ->paginate($this->cant);
                        

        return view('livewire.category.category-component', [
            'categories' => $categories
            
        ]);
    }

    public function mount(){
       
    }

    //Abre el modal
    public function create(){

        $this->Id=0;

        $this->reset(['name']);
        $this->resetErrorBag();
        $this->dispatch('open-modal', 'modalCategory');
    }

     /**
     * Guarda una nueva categoría en la base de datos.
     * 
     * Valida los datos antes de guardarlos y notifica al usuario del resultado.
     */
    public function store(){
        
        $rules = [
            'name' => 'required|min:5|max:255|unique:categories'
        ];
        $messages = [
            'name.required' => 'El nombre es requerido',
            'name.min' => 'Mínimo 5 caracteres',
            'name.max' => 'Maximo 255 caracteres',
            'name.unique' => 'Esta categoría ya esta creada'
        ];

        $this->validate($rules, $messages);

        $category = new Category();
        $category -> name = $this->name;
        $category -> save();

        $this->dispatch('close-modal', 'modalCategory');
        $this->dispatch('msg', 'Categoria creada correctamente');

        $this->reset(['name']);
    }

        /**
     * Abre el modal para editar una categoría existente.
     * 
     * @param Category $category Instancia del modelo de categoría a editar
     */
    public function edit(Category $category){

        $this->reset(['name']);
        $this->Id = $category->id;

        $this->name = $category->name;

        $this->dispatch('open-modal', 'modalCategory');
    }

        /**
     * Actualiza los datos de una categoría existente.
     * 
     * @param Category $category Instancia del modelo a actualizar
     */
    public function update(Category $category){

        $rules = [
            'name' => 'required|min:5|max:255|unique:categories,id,'.$this->Id
        ];
        $messages = [
            'name.required' => 'El nombre es requerido',
            'name.min' => 'Mínimo 5 caracteres',
            'name.max' => 'Maximo 255 caracteres',
            'name.unique' => 'Esta categoría ya esta creada'
        ];

        $this->validate($rules, $messages);

        $category->name = $this->name;
        $category->update();

        $this->dispatch('close-modal', 'modalCategory');
        $this->dispatch('msg', 'Categoria editada correctamente');

        $this->reset(['name']);
    }
    
        /**
     * Elimina una categoría de la base de datos.
     * 
     * @param int $id ID de la categoría a eliminar
     */
    #[On('destroyCategory')]
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        $this->dispatch('msg', 'La categoria ha sido eliminada correctamente');
    }
}
