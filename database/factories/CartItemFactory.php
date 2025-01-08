<?php

namespace Database\Factories;


use App\Models\Cart;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CartItem>
 */
class CartItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::inRandomOrder()->first();
        return [
            'cart_id' => Cart::factory(),
            'product_id' => Product::factory(),
            'quantity' => $this->faker->numberBetween(1,20),
            'unit_price' => 0, 

        ];
    }
}
