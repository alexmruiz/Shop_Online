<?php

namespace App\Livewire\Category;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\Category;
use Livewire\Attributes\On;

#[Title('Categorias')]
class CategoryComponent extends Component
{
    use WithPagination;
    //Propiedades de la clase
    public $totalRegistros=0;
    public $search='';
    public $cant=5;
    //Propiedades modelo
    public $name = '';
    public $Id;
    
    public function render()
    {
        if($this->search==''){
            $this->resetPage();
        }

        $this->dispatch('open-modal', 'modalProduct');

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

    public function create(){

        $this->Id=0;

        $this->reset(['name']);
        $this->resetErrorBag();
        $this->dispatch('open-modal', 'modalCategory');
    }

    public function store(){
        //dd(1);
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

    public function edit(Category $category){

        $this->reset(['name']);
        $this->Id = $category->id;

        $this->name = $category->name;

        $this->dispatch('open-modal', 'modalCategory');
    }

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
    
    #[On('destroyCategory')]
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        $this->dispatch('msg', 'La categoria ha sido eliminada correctamente');
    }
}
