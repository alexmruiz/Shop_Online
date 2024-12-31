<?php

namespace App\Livewire\Home;

use App\Livewire\Product\ProductComponent;
use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Inicio')]
class Inicio extends Component
{
    public $Id=0;
    public $totalRegistrosClient = 0;
    public $totalRegistrosProduct = 0;
    public $category_id;
    public function render()
    {
        $this->totalRegistrosProduct = Product::count();
        $this->totalRegistrosClient = User::where ('role', 'user')-> count();

        $productComponent = new ProductComponent();
        $createButtonHtml = $productComponent->renderCreateButton();
        return view('livewire.home.inicio', data: [
         
            'createButtonHtml' => $createButtonHtml,

        ]);
    }
    #[Computed()]
    public function categories()
    {
        return Category::all();
    }
    
}
