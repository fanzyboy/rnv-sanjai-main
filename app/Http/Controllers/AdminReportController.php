<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Preorder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


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

    public function export(Request $request)
{
    $month = $request->month;
    $year = $request->year;

    // Ambil data yang sama dengan yang tampil di tabel
    $orders = \App\Models\Order::where('status', 'selesai');
    $preorders = \App\Models\Preorder::where('status', 'selesai');

    if ($month) {
        $orders->whereMonth('created_at', $month);
        $preorders->whereMonth('created_at', $month);
    }
    if ($year) {
        $orders->whereYear('created_at', $year);
        $preorders->whereYear('created_at', $year);
    }

    $dataOrders = $orders->get();
    $dataPreorders = $preorders->get();

    // Nama file
    $fileName = 'Laporan_Penjualan_' . ($month ?? 'Semua') . '_' . ($year ?? 'Semua') . '.csv';

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['Tipe', 'Kode', 'Pelanggan', 'Total Bayar', 'Tanggal'];

    $callback = function() use($dataOrders, $dataPreorders, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($dataOrders as $order) {
            fputcsv($file, ['Order', '#ORD'.$order->id, $order->user->name ?? '-', $order->total_amount, $order->created_at->format('d-m-Y')]);
        }

        foreach ($dataPreorders as $po) {
            fputcsv($file, ['Preorder', '#PO'.$po->id, $po->user->name ?? '-', $po->total_amount, $po->created_at->format('d-m-Y')]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}
