<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi: order_id atau preorder_id boleh kosong salah satu
        $request->validate([
            'order_id'    => 'nullable|exists:orders,id',
            'preorder_id' => 'nullable|exists:preorders,id',
            'product_id'  => 'required|exists:products,id',
            'rating'      => 'required|integer|min:1|max:5',
            'review'      => 'nullable|string|max:255'
        ]);

        // 2. Gunakan updateOrCreate untuk mencegah duplikasi ulasan
        // Logic: Laravel akan mencari record berdasarkan user_id, product_id, dan (order_id ATAU preorder_id)
        Rating::updateOrCreate(
            [
                'user_id'     => auth()->id(),
                'product_id'  => $request->product_id,
                'order_id'    => $request->order_id,    // Jika order biasa, ini ada isinya
                'preorder_id' => $request->preorder_id, // Jika preorder, ini ada isinya
            ],
            [
                'rating' => $request->rating,
                'review' => $request->review,
            ]
        );

        return back()->with('success', 'Terima kasih atas ulasan Anda! ⭐');
    }
}
