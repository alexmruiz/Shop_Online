<?php

use App\Livewire\Category\CategoryComponent;
use App\Livewire\Product\ProductComponent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Livewire\Home\Inicio;
use App\Livewire\Category\CategoryShow;
use App\Livewire\Client\ClientComponent;
use App\Livewire\Product\ProductShow;
use App\Livewire\Product\PublicProducts;

//Route::get('/', PublicProducts::class)->name('home');
Route::get('/', function(){
    return view('components.layouts.public_access');
})->name('home');

Route::get('/dashboard',  Inicio::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {

/*Ruta Logout*/
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

/*Ruta Categorias*/
Route::get('/categorias', CategoryComponent::class)->name('category');
Route::get('/ver_categoria/{category}', CategoryShow::class)->name('categoryShow');

/*Ruta Productos*/
Route::get('/productos', ProductComponent::class)->name('product');
Route::get('/ver_producto/{product}', ProductShow::class)->name('productShow');

/*Ruta Clientes*/
Route::get('/clientes', ClientComponent::class)->name('client');

});

