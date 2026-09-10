<?php

namespace App\Models;

use App\Models\Scopes\ActiveProductScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

#[ScopedBy(ActiveProductScope::class)]
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'is_active',
        'image',
        'category_id',
        'external_id',
        'stock',
        'reserved_stock'
    ];

    /**
     * Summary of category
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Category, Product>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Summary of activeLabel
     * @return Attribute
     */
    protected function activeLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->attributes['is_active']
                    ? '<span class="badge badge-success">Activo</span>'
                    : '<span class="badge badge-danger">Inactivo</span>';
            }
        );
    }

    /**
     * Summary of topSellingProducts
     * @param mixed $limit
     * @return \Illuminate\Support\Collection<int, \stdClass>
     */
    public static function topSellingProducts($limit = 5)
    {
        return DB::table('cart_items')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(cart_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get();
    }

}
