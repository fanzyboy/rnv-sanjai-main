<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preorder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'price_id',
        'qty',
        'total_amount',
        'bukti_transfer',
        'metode_pembayaran',
        'status',
        'tanggal_preorder',
        'deskripsi',
        'refund_amount',
        'bukti_admin',
        'refund_at',
        'nomor_rekening',
        'nama_bank'
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke harga produk (product_prices)
    public function price()
    {
        return $this->belongsTo(ProductPrice::class, 'price_id');
    }
    public function rating()
    {
        return $this->hasOne(Rating::class);
    }
}
