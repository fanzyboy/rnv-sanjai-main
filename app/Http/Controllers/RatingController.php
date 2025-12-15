<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id'   => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'review'     => 'nullable|string|max:255'
        ]);

        Rating::updateOrCreate(
            [
                'user_id'    => auth()->id(),
                'order_id'   => $request->order_id,
                'product_id' => $request->product_id,
            ],
            [
                'rating' => $request->rating,
                'review' => $request->review,
            ]
        );

        return back()->with('success', 'Terima kasih atas rating Anda ⭐');
    }
}
