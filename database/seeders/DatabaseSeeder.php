<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuarios
        User::factory(10)->create([
            'password' => bcrypt('user_password'),
        ]);

        User::factory(1)->create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => bcrypt('admin852'),
            'role' => 'admin',
        ]);

        // Crear categorías y productos
        Category::factory(10)->create()->each(function ($category) {
            Product::factory(5)->create(['category_id' => $category->id]);
        });

        // Generar pedidos y artículos
        $faker = \Faker\Factory::create(); // Generador de datos aleatorios
        User::all()->each(function ($user) use ($faker) {
            // Cada usuario tiene de 1 a 3 pedidos
            Cart::factory()->count(fake()->numberBetween(1, 3))->create(['user_id' => $user->id])
                ->each(function ($cart) use ($faker) {
                    // Cada pedido tiene de 1 a 5 artículos
                    $products = Product::inRandomOrder()->take($faker->numberBetween(1, 5))->get();
                    foreach ($products as $product) {
                        CartItem::factory()->create([
                            'cart_id' => $cart->id,
                            'product_id' => $product->id,
                            'unit_price' => $product->price, // Establecer el precio del producto
                        ]);
                    }
                });
        });
    }
}
