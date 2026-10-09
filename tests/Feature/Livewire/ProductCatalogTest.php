<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Product\ProductCatalog;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_authenticated_users_see_the_cart_expiration_notice(): void
    {
        $this->actingAs(\App\Models\User::factory()->create());
        Product::factory()->create();

        Livewire::test(ProductCatalog::class)
            ->assertSee(__('cart.expiration_notice'));
    }

    #[Test]
    public function test_guests_do_not_see_the_cart_expiration_notice(): void
    {
        Product::factory()->create();

        Livewire::test(ProductCatalog::class)
            ->assertDontSee(__('cart.expiration_notice'));
    }

    #[Test]
    public function toggle_anade_y_quita_un_favorito(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['is_active' => true]);

        Livewire::actingAs($user)->test(ProductCatalog::class)
            ->call('toggleFavorite', $product->id);
        $this->assertDatabaseHas('favorite_products', ['user_id' => $user->id, 'product_id' => $product->id]);

        Livewire::actingAs($user)->test(ProductCatalog::class)
            ->call('toggleFavorite', $product->id);
        $this->assertDatabaseMissing('favorite_products', ['user_id' => $user->id, 'product_id' => $product->id]);
    }

    #[Test]
    public function test_cannot_favorite_an_inactive_product(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['is_active' => false]);

        $this->expectException(ModelNotFoundException::class);

        Livewire::actingAs($user)->test(ProductCatalog::class)
            ->call('toggleFavorite', $product->id);
    }
}
