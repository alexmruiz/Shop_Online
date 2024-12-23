<?php

namespace App\Livewire\Category;

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\category;

#[Title('Ver Categorias')]
class CategoryShow extends Component
{
    public category $category;
    public function render()
    {
        return view('livewire.category.category-show');
    }
}
