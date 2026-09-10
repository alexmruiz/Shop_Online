<?php

namespace App\Models;

use App\Observers\CategoryObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([CategoryObserver::class])]
class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

}
