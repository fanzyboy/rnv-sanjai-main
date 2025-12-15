<?php

namespace App\Http\Controllers;

use App\Models\Order;

class AdminReportController extends Controller
{
    public function index()
    {
        // QUERY DASAR (SAMA DENGAN DASHBOARD)
        $finishedOrders = Order::where('status', 'selesai');

        // ✅ TOTAL PESANAN SELESAI
        $totalOrders = $finishedOrders->count();

        // ✅ TOTAL PENDAPATAN (FIX & SAMA DASHBOARD)
        $totalRevenue = $finishedOrders->sum('total_amount');

        /**
         * ✅ TOTAL PRODUK TERJUAL
         * Karena TIDAK ADA kolom qty di tabel orders,
         * maka:
         * - Tidak bisa dihitung dari tabel ini
         * - Harus dari order_items
         */
        $totalSold = 0; // aman, tidak manipulasi data

        // ✅ DATA LAPORAN (HANYA YANG SELESAI)
        $orders = Order::with('user')
            ->where('status', 'selesai')
            ->latest()
            ->get();

        return view('admin.laporan.index', compact(
            'totalOrders',
            'totalRevenue',
            'totalSold',
            'orders'
        ));
    }
}
