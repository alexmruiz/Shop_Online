<?php

namespace Tests\Feature\Repository;

use App\Models\Category;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ProductRepository $repo;

    /**
     * Configura el entorno de prueba.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->repo = new ProductRepository();
    }

    #[Test]
    public function it_returns_all_categories(): void
    {
        Category::factory()->count(3)->create();

        $categories = $this->repo->getAllCategories();

        $this->assertCount(3, $categories);
    }

    #[Test]
    public function it_returns_products_paginated(): void
    {
        Product::factory()->count(10)->create();

        $result = $this->repo->searchAndFilter(null, null, 5);

        $this->assertCount(5, $result->items());
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    #[Test]
    public function it_filters_products_by_category(): void
    {
        $cat1 = Category::factory()->create();
        $cat2 = Category::factory()->create();

        Product::factory()->count(3)->create(['category_id' => $cat1->id]);
        Product::factory()->count(2)->create(['category_id' => $cat2->id]);

        $result = $this->repo->searchAndFilter(null, $cat1->id, 5);

        $this->assertCount(3, $result->items());
        
        // Uso de la colección de Eloquent para verificar que todos pertenezcan a la categoría 1
        $this->assertTrue(
            $result->getCollection()->every(fn ($product) => $product->category_id === $cat1->id)
        );
    }

    #[Test]
    public function it_filters_products_by_search_term(): void
    {
        Product::factory()->create(['name' => 'Laptop']);
        Product::factory()->create(['name' => 'Smartphone']);

        $result = $this->repo->searchAndFilter('Lap', null, 5);

        $this->assertCount(1, $result->items());
        $this->assertEquals('Laptop', $result->first()->name);
    }
}