<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Preorder;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    public function index()
    {
        $month = request('month');
        $year  = request('year');

        // ===============================
        // ORDERS (SELESAI)
        // ===============================
        $ordersQuery = Order::where('status', 'selesai');

        if ($month) {
            $ordersQuery->whereMonth('created_at', $month);
        }

        if ($year) {
            $ordersQuery->whereYear('created_at', $year);
        }

        // ===============================
        // PREORDERS (SELESAI)
        // ===============================
        $preordersQuery = Preorder::where('status', 'selesai');

        if ($month) {
            $preordersQuery->whereMonth('created_at', $month);
        }

        if ($year) {
            $preordersQuery->whereYear('created_at', $year);
        }

        // ===============================
        // JUMLAH PESANAN
        // ===============================
        $totalOrderCount = $ordersQuery->count();
        $totalPreorderCount = $preordersQuery->count();
        $totalOrders = $totalOrderCount + $totalPreorderCount;

        // ===============================
        // TOTAL PENDAPATAN
        // ===============================
        $totalRevenue =
            $ordersQuery->sum('total_amount') +
            $preordersQuery->sum('total_amount');

        // ===============================
        // PRODUK TERJUAL
        // ===============================

        // ORDERS → hitung product_id di order_items
        $orderIds = $ordersQuery->pluck('id');

        $orderSold = DB::table('order_items')
            ->whereIn('order_id', $orderIds)
            ->count('product_id');

        // PREORDERS → hitung price_id
        $preorderSold = $preordersQuery->count('price_id');

        $totalSold = $orderSold + $preorderSold;

        // ===============================
        // DATA TABEL
        // ===============================
        $orders = $ordersQuery->with('user')->latest()->get();
        $preorders = $preordersQuery->with('user')->latest()->get();

        return view('admin.laporan.index', compact(
            'totalOrders',
            'totalOrderCount',
            'totalPreorderCount',
            'totalRevenue',
            'totalSold',
            'orders',
            'preorders'
        ));
    }
}
