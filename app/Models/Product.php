<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description','price','is_active', 'image'];

    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function client(){
        return $this->belongsTo(Client::class);
    }

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
}
