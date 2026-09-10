<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Product\ProductCatalog;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_see_the_cart_expiration_notice(): void
    {
        $this->actingAs(\App\Models\User::factory()->create());
        Product::factory()->create();

        Livewire::test(ProductCatalog::class)
            ->assertSee(__('cart.expiration_notice'));
    }

    public function test_guests_do_not_see_the_cart_expiration_notice(): void
    {
        Product::factory()->create();

        Livewire::test(ProductCatalog::class)
            ->assertDontSee(__('cart.expiration_notice'));
    }
}
