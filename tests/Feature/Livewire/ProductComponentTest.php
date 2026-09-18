<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Product\ProductComponent;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_product_component_successfully(): void
    {
        Product::factory()->create(['name' => 'Producto Test']);

        Livewire::test(ProductComponent::class)
            ->assertStatus(200)
            ->assertSee('Producto Test');
    }

    public function test_filters_products_by_search_term(): void
    {
        Product::factory()->create(['name' => 'iPhone 13', 'is_active' => 1]);
        Product::factory()->create(['name' => 'Samsung Galaxy S21', 'is_active' => 1]);

        Livewire::test(ProductComponent::class)
                ->set('search', 'iPhone')
                ->assertSee('iPhone 13')
                ->assertDontSee('Samsung Galaxy S21');
    }

    public function test_paginates_products_correctly(): void
    {
        // Creamos 7 productos activos con nombres únicos
        for ($i = 1; $i <= 7; $i++) {
            Product::factory()->create(['name' => "Product {$i}", 'is_active' => 1]);
        }

        $latestProducts = Product::orderByDesc('id')->get()->values();

        Livewire::test(ProductComponent::class)
            ->set('cant', 5)
            // Página 1: Verifica el más reciente y el límite de la pág 1
            ->assertSee($latestProducts[0]->name)
            ->assertSee($latestProducts[4]->name)
            ->assertDontSee($latestProducts[5]->name)
            // Navega a Página 2
            ->call('gotoPage', 2)
            ->assertSee($latestProducts[5]->name)
            ->assertSee($latestProducts[6]->name);
    }

    public function test_creates_a_new_product_and_dispatches_events(): void
    {
        $category = Category::factory()->create();

        Livewire::test(ProductComponent::class)
            ->set('name', 'Nuevo Producto')
            ->set('description', 'Descripción del nuevo producto')
            ->set('price', 99.99)
            ->set('stock', 10)
            ->set('category_id', $category->id)
            ->call('store')
            ->assertDispatched('close-modal', 'modalProduct')
            ->assertDispatched('msg', 'Producto creado correctamente');

        $this->assertDatabaseHas('products', [
            'name' => 'Nuevo Producto',
            'description' => 'Descripción del nuevo producto',
            'price' => 99.99,
            'category_id' => $category->id,
        ]);
    }

    public function test_updates_an_existing_product(): void
    {
        $product = Product::factory()->create();

        Livewire::test(ProductComponent::class)
            ->call('edit', $product)
            ->set('name', 'Producto Actualizado')
            ->call('update', $product)
            ->assertDispatched('close-modal', 'modalProduct')
            ->assertDispatched('msg', 'Producto editado correctamente');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Producto Actualizado',
        ]);
    }

    public function test_deletes_a_product_on_destroy_event(): void
    {
        $product = Product::factory()->create();

        Livewire::test(ProductComponent::class)
            ->dispatch('destroyProduct', $product->id)
            ->assertDispatched('msg', 'El producto ha sido eliminado correctamente');

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
}