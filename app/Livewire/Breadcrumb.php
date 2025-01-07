<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class Breadcrumb extends Component
{
    public $breadcrumbs = [];

    #[On ('update-breadcrumbs')]
    public function updateBreadcrumbs(array $crumbs)
    {
        $this->breadcrumbs = $crumbs;
    }

    public function render()
    {
        return view('livewire.breadcrumb');
    }
}
