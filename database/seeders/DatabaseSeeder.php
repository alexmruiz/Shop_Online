<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create([
            'password' => bcrypt('user_password'),
        ]);

        User::factory(1)->create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => bcrypt('admin852'),
            'role' => 'admin',
        ]);

        Category::factory(10)->create()->each(function ($category) {
            Product::factory(5)->create(['category_id' => $category->id]);
        });
    }
}
