<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    protected $fillable = ['user_id', 'produk_id', 'variasi_id', 'qty'];

    public function produk()
    {
        return $this->belongsTo(Product::class, 'produk_id');
    }

    public function variasi()
    {
        return $this->belongsTo(ProductPrice::class, 'variasi_id');
    }
}
