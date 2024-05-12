<?php

namespace App\Models;

use App\Models\Order_item;
use App\Models\ProductRate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price'];

    public function orderItems(): HasMany
    {
        return $this->hasMany(Order_item::class);
    }

    public function rate():HasMany
    {
        $this->hasMany(ProductRate::class);
    }

}
