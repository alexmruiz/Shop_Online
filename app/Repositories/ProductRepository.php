<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\Category;

class ProductRepository
{
    public function searchAndFilter(?string $search, ?int $category, int $perPage)
    {
        $query = Product::query();

        if ($category) {
            $query->where('category_id', $category);
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function getAllCategories()
    {
        return Category::all();
    }
}
