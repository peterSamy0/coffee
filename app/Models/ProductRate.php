<?php

namespace App\Models;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductRate extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'user_id', 'rate'];

    public function product():HasMany
    {
        $this->hasMany(Product::class);
    }

    public function user():HasMany
    {
        $this->hasMany(User::class);
    }
}
