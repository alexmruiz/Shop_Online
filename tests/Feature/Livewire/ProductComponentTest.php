<?php

namespace Tests\Feature;

use App\Livewire\Product\ProductComponent;
use App\Models\Category;
use Tests\TestCase;
use App\Models\Product;
use Livewire\Livewire;

use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductComponentTest extends TestCase
{
    use RefreshDatabase; //Limpia la base de datos después de cada prueba

    /** @test */
    public function it_shows_products()
    {
        $product = Product::factory()->create(['name' => 'Producto Test']);

        Livewire::test(ProductComponent::class)
            ->assertSee('Producto Test');
    }

    /** @test */
    public function it_filters_products_by_search(): void
    {
        // Crear productos de prueba
        $iphone = Product::factory()->create(['name' => 'iPhone 13']);
        $samsung = Product::factory()->create(['name' => 'Samsung Galaxy S21']);

        // Testear el componente con búsqueda
        Livewire::test(ProductComponent::class)
            ->set('search', 'iPhone') // Establece el término de búsqueda
            ->assertSee('iPhone 13') // Verifica que se vea el producto iPhone
            ->assertDontSee('Samsung Galaxy S21'); // Verifica que no se vea el producto Samsung
    }

    /** @test */
    public function it_paginates_products(): void
    {
        // Creamos 7 productos de prueba. La paginación está configurada para mostrar 5 por página.
        $products = Product::factory()->count(7)->create();

        $productsDesc = $products->sortByDesc('id')->values(); // Ordenamos por ID descendente

        Livewire::test(ProductComponent::class)
            ->assertSee($productsDesc[0]->name) // Página 1, producto más reciente
            ->assertSee($productsDesc[1]->name)
            ->assertDontSee($productsDesc[5]->name);
    }


    /** @test */
    public function it_creates_a_product(): void
    {
        $category = \App\Models\Category::factory()->create();

        Livewire::test(ProductComponent::class)
            ->set('name', 'Nuevo Producto')
            ->set('description', 'Descripción del nuevo producto')
            ->set('price', 99.99)
            ->set('category_id', $category->id)
            ->call('store')
            ->assertDispatched('msg', 'Producto creado correctamente');

        // Verificamos que el producto se haya creado en la base de datos
        $this->assertDatabaseHas('products', [
            'name' => 'Nuevo Producto',
            'description' => 'Descripción del nuevo producto',
            'price' => 99.99,
        ]);
    }

    /** @test */
    public function it_updates_a_product(): void
    {
        $category = \App\Models\Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        Livewire::test(ProductComponent::class)
            ->call('edit', $product) // Llama al método edit con el producto
            ->set('name', 'Producto Actualizado')
            ->call('update', $product) // Llama al método update con el producto
            ->assertDispatched('msg', 'Producto editado correctamente');


        // Verificamos que el producto se haya actualizado en la base de datos
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Producto Actualizado',
        ]);
    }

    /** @test */
    public function it_deletes_a_product(): void
    {
        $product = Product::factory()->create(['name' => 'Producto a eliminar']);

        Livewire::test(ProductComponent::class)
            ->call('destroy', $product->id) // Llama al método delete con el ID del producto
            ->assertDispatched('msg', 'El producto ha sido eliminado correctamente'); 

        // Verificamos que el producto se haya eliminado de la base de datos
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
        
    }



}
