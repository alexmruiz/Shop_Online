<?php

namespace App\Livewire\Home;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Dashboard')]
class DashboardComponent extends Component
{
    public int $totalRegistrosClient = 0;
    public int $totalRegistrosProduct = 0;
    public int $categoryId;
    public Collection $topSellingProducts;

    #[Computed()]
    public function categories()
    {
         return Cache::rememberForever('categories', function () {
            return Category::all();
        });
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


        return view('livewire.home.dashboard-component', data: [


            'topSellingProducts' => $this->topSellingProducts,
        ]);
    }
}
