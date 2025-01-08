<?php

use App\Livewire\Cart\CartComponent;
use App\Livewire\Cart\CartConfirmed;
use App\Livewire\Cart\CheckoutForm;
use App\Livewire\Category\CategoryComponent;
use App\Livewire\Product\ProductComponent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Livewire\Home\Inicio;
use App\Livewire\Category\CategoryShow;
use App\Livewire\Client\ClientComponent;
use App\Livewire\Client\ClientShow;
use App\Livewire\Orders\MyOrders;
use App\Livewire\Product\ProductShow;
use App\Livewire\Product\PublicProducts;


Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';


//Inicio
Route::get('/', PublicProducts::class)->name('start')->middleware('guest');

//Logout
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

// Usuario rol: 'admin'
Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/dashboard', Inicio::class)->name('dashboard');
    // Categorías
    Route::get('/categorias', CategoryComponent::class)->name('category');
    Route::get('/ver_categoria/{category}', CategoryShow::class)->name('categoryShow');
    // Productos
    Route::get('/productos', ProductComponent::class)->name('product');
    Route::get('/ver_producto/{product}', ProductShow::class)->name('productShow');
    // Clientes
    Route::get('/clientes', ClientComponent::class)->name('client');
    //Descargar factura en ver clientes
    Route::get('/ver_cliente/{user}', ClientShow::class)->name('clientShow');
     //Descargar facturas(Ver clientes)
    Route::get('/invoice_downloaded/{id}', [ClientShow::class, 'downloadInvoice'])->name('downloaded.invoice');
});

// Usuarios autenticados rol: 'user'
Route::middleware(['auth', 'role:user'])->group(function () {

    // Inicio clientes
    Route::get('/home', PublicProducts::class)->name('home');

    //Cesta de la compra
    Route::get('/cesta_compra', CartComponent::class)->name('cart');

    //Checkout
    Route::get('/checkout', CheckoutForm::class)->name('checkout');

    //Confirmed
    Route::get('/pedido_corfirmado', CartConfirmed::class)->name('confirmed');

    //Descargar factura(Pedido aceptado)
    Route::get('/invoice/download', [CartConfirmed::class, 'generateInvoice'])->name('generate.invoice');

    //My Orders
    Route::get('/mis_pedidos', MyOrders::class)->name('myOrders');

    //Descargar facturas(Mis facturas)
    Route::get('/invoice/download/{id}', [MyOrders::class, 'downloadInvoice'])->name('download.invoice');

}); 
