<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Http;

class ProductService
{
    protected $apiUrl = 'https://api.escuelajs.co/api/v1/products'; // Fake Store API

    public function importProducts()
    {
        $response = Http::get($this->apiUrl);

        if (!$response->successful()) {
            return false;
        }

        $products = $response->json();

        foreach ($products as $p) {

            $categoryName = $p['category']['name'] ?? 'Sin Categoria';

            //Crear o bucar categoria
            $category = Category::firstOrCreate([
                'name' => $categoryName,
            ]);

            //Crear o actualizar producto
            Product::updateOrCreate(
                ['external_id' => $p['id']],
                [
                    'name' => $p['title'],
                    'description' => $p['description'],
                    'price' => $p['price'],
                    'category_id' => $category->id,
                    'is_active' => 1,
                ]
            );
        }
        return true;
    }
}
