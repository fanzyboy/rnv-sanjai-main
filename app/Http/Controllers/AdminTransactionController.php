<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Preorder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminTransactionController extends Controller
{
    // ===============================
    // HALAMAN LIST TRANSAKSI
    // ===============================
    public function index()
    {
        $orders = Order::with(['user', 'payments'])
            ->latest()
            ->paginate(10, ['*'], 'orders_page');

        $preorders = Preorder::with(['user', 'price'])
            ->latest()
            ->paginate(10, ['*'], 'preorders_page');

        return view('admin.transactions.index', compact('orders', 'preorders'));
    }

    // ===============================
    // UPDATE STATUS ORDER
    // ===============================
    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status'        => 'required|in:pending,proses,selesai,ditolak',
            'refund_amount' => 'nullable|numeric|min:0',
            'bukti_admin'   => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'type'          => 'required|in:order',
        ]);

        $order = Order::findOrFail($id);

        // ===============================
        // DITOLAK + REFUND TRANSFER
        // ===============================
        if ($request->status === 'ditolak' && $request->hasFile('bukti_admin')) {

            DB::beginTransaction();

            try {
                $path = $request->file('bukti_admin')
                    ->store('refund_proofs', 'public');

                $order->update([
                    'status'        => 'ditolak',
                    'total_amount'  => 0,
                    'refund_amount' => $request->refund_amount,
                    'bukti_admin'   => $path,
                ]);

                DB::commit();

                return back()->with(
                    'success',
                    'Order berhasil ditolak & refund dikonfirmasi'
                );

            } catch (\Exception $e) {
                DB::rollBack();

                if (isset($path)) {
                    Storage::disk('public')->delete($path);
                }

                return back()->with(
                    'error',
                    'Gagal memproses refund order: ' . $e->getMessage()
                );
            }
        }

        // ===============================
        // DITOLAK TANPA REFUND (COD)
        // ===============================
        if ($request->status === 'ditolak') {
            $order->update([
                'status'       => 'ditolak',
                'total_amount' => 0,
            ]);

            return back()->with(
                'success',
                'Order ditolak tanpa refund'
            );
        }

        // ===============================
        // STATUS BIASA
        // ===============================
        $order->update([
            'status' => $request->status,
        ]);

        return back()->with(
            'success',
            'Status order diperbarui menjadi ' . ucfirst($request->status)
        );
    }

    // ===============================
    // UPDATE STATUS PREORDER
    // ===============================
    public function updatePreorderStatus(Request $request, $id)
    {
        $request->validate([
            'status'        => 'required|in:pending,proses,selesai,ditolak',
            'refund_amount' => 'nullable|numeric|min:0',
            'bukti_admin'   => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'type'          => 'required|in:preorder',
        ]);

        $preorder = Preorder::findOrFail($id);

        // ===============================
        // DITOLAK + REFUND TRANSFER
        // ===============================
        if ($request->status === 'ditolak' && $request->hasFile('bukti_admin')) {

            DB::beginTransaction();

            try {
                $path = $request->file('bukti_admin')
                    ->store('refund_proofs', 'public');

                $preorder->update([
                    'status'        => 'ditolak',
                    'total_amount'  => 0,
                    'refund_amount' => $request->refund_amount,
                    'bukti_admin'   => $path,
                ]);

                DB::commit();

                return back()->with(
                    'success',
                    'Preorder berhasil ditolak & refund dikonfirmasi'
                );

            } catch (\Exception $e) {
                DB::rollBack();

                if (isset($path)) {
                    Storage::disk('public')->delete($path);
                }

                return back()->with(
                    'error',
                    'Gagal memproses refund preorder: ' . $e->getMessage()
                );
            }
        }

        // ===============================
        // DITOLAK TANPA REFUND
        // ===============================
        if ($request->status === 'ditolak') {
            $preorder->update([
                'status'       => 'ditolak',
                'total_amount' => 0,
            ]);

            return back()->with(
                'success',
                'Preorder ditolak tanpa refund'
            );
        }

        // ===============================
        // STATUS BIASA
        // ===============================
        $preorder->update([
            'status' => $request->status,
        ]);

        return back()->with(
            'success',
            'Status preorder diperbarui menjadi ' . ucfirst($request->status)
        );
    }
}
