<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Repositories\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ProductRepository $repo;

    /**
     * Configura el entorno de prueba
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new ProductRepository();
    }

    /** @test */
    public function it_returns_all_categories()
    {
        Category::factory()->count(3)->create();

        $categories = $this->repo->getAllCategories();

        $this->assertCount(3, $categories);
    }

    /** @test */
    public function it_returns_products_paginated()
    {
        Product::factory()->count(10)->create();

        $result = $this->repo->searchAndFilter(null, null, 5);

        // Contamos los elementos de la página actual
        $this->assertCount(5, $result->items()); 
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
    }

    /** @test */
    public function it_filters_products_by_category()
    {
        $cat1 = Category::factory()->create();
        $cat2 = Category::factory()->create();

        Product::factory()->count(3)->create(['category_id' => $cat1->id]);
        Product::factory()->count(2)->create(['category_id' => $cat2->id]);

        $result = $this->repo->searchAndFilter(null, $cat1->id, 5);

        $this->assertCount(3, $result->items());

        foreach ($result->items() as $product) {
            $this->assertEquals($cat1->id, $product->category_id);
        }
    }

    /** @test */
    public function it_filters_products_by_search_term()
    {
        Product::factory()->create(['name' => 'Laptop']);
        Product::factory()->create(['name' => 'Smartphone']);

        $result = $this->repo->searchAndFilter('Lap', null, 5);

        $this->assertCount(1, $result->items());
        $this->assertEquals('Laptop', $result->first()->name);
    }
}
