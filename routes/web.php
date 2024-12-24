<?php

use App\Livewire\Category\CategoryComponent;
use App\Livewire\Product\ProductComponent;
use Illuminate\Support\Facades\Route;
use App\Livewire\Home\Inicio;
use App\Livewire\Category\CategoryShow;
use App\Livewire\Product\ProductShow;

Route::view('/', 'welcome');

Route::get('/dashboard',  Inicio::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';


Route::get('/categorias', CategoryComponent::class)->name('category');
Route::get('/productos', ProductComponent::class)->name('product');
Route::get('/ver_categoria/{category}', CategoryShow::class)->name('categoryShow');
Route::get('/ver_producto', ProductShow::class)->name('productShow');
