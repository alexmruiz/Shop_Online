<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Enums\CartStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(1)->create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => Hash::make('admin852'),
            'role' => 'admin',
        ]);

        User::factory(1)->create([
            'name' => 'Test User',
            'email' => 'test@testuser.com',
            'password' => Hash::make('testuser'),
            'role' => 'user',
        ]);

        $users = User::factory()->count(10)->create([
            'password' => Hash::make('user_password'),
        ]);
        Product::factory()->count(30)->create(['reserved_stock' => 0]);

        $faker = \Faker\Factory::create();
        $users->each(function ($user) use ($faker) {
            // Cada usuario tiene de 1 a 3 pedidos
            Cart::factory()->count(fake()->numberBetween(1, 3))->create([
                'user_id' => $user->id,
                'status' => CartStatus::PENDING,
            ])
                ->each(function ($cart) use ($faker) {
                    // Cada pedido tiene de 1 a 5 artículos
                    $products = Product::inRandomOrder()->take($faker->numberBetween(1, 5))->get();
                    foreach ($products as $product) {
                        $availableStock = $product->stock - $product->reserved_stock;

                        if ($availableStock < 1) {
                            continue;
                        }

                        $quantity = $faker->numberBetween(1, min(5, $availableStock));

                        CartItem::factory()->create([
                            'cart_id' => $cart->id,
                            'product_id' => $product->id,
                            'quantity' => $quantity,
                            'unit_price' => $product->price,
                            'reserved_until' => fake()->dateTimeBetween('now', '+1 hours'),
                        ]);

                        $product->increment('reserved_stock', $quantity);
                    }
                });
        });
    }
}
