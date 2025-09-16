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
    public $Id = 0;
    public $totalRegistrosClient = 0;
    public $totalRegistrosProduct = 0;
    public $category_id;
    public $topSellingProducts = [];

    #[Computed()]
    public function categories()
    {
        return Category::all();
    }

    public function mount()
    {
        // Inicializar totales
        $this->totalRegistrosProduct = Product::count();
        $this->totalRegistrosClient = User::where('role', 'user')->count();

        // Obtener productos más vendidos
        $this->topSellingProducts = Product::topSellingProducts();
    }

    public function render()
    {
        $this->totalRegistrosProduct = Product::count();
        $this->totalRegistrosClient = User::where('role', 'user')->count();


        return view('livewire.home.inicio', data: [


            'topSellingProducts' => $this->topSellingProducts,
        ]);
    }
}
