<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;

class Rating extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'product_id',
        'rating',
        'review'
    ];

    // ✅ Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ✅ Relasi ke Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // ✅ Relasi ke Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
