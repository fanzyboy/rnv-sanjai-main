<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Preorder;
use App\Models\ProductPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PreorderController extends Controller
{
    /**
     * FORM PREORDER
     */
    public function create(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu untuk melakukan pre-order.');
        }

        $variasiId = $request->query('variasi_id');

        if (!$variasiId) {
            return redirect()->route('produk')
                ->with('error', 'Variasi produk tidak ditentukan.');
        }

        $price = ProductPrice::with('product')->find($variasiId);

        if (!$price) {
            return redirect()->route('produk')
                ->with('error', 'Data variasi produk tidak ditemukan.');
        }

        $qty = $request->query('qty', 1);

        return view('preorder.create', compact('price', 'qty'));
    }

    /**
     * SIMPAN PREORDER
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // =========================
        // VALIDASI
        // =========================
        $validated = $request->validate([
            'price_id'          => 'required|exists:product_prices,id',
            'qty'               => 'required|integer|min:1',
            'deskripsi'         => 'nullable|string|max:255',
            'metode_pembayaran' => 'required|in:transfer,cod',
            'bukti_transfer'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Transfer → bukti WAJIB
        if (
            $validated['metode_pembayaran'] === 'transfer'
            && !$request->hasFile('bukti_transfer')
        ) {
            return back()->withErrors([
                'bukti_transfer' => 'Bukti transfer wajib diupload.'
            ])->withInput();
        }

        $user = Auth::user();

        try {
            $price = ProductPrice::with('product')->findOrFail($validated['price_id']);
        } catch (\Exception $e) {
            return back()->with('error', 'Variasi produk tidak ditemukan.');
        }

        try {
            // =========================
            // HITUNG TOTAL AMOUNT
            // =========================
            $totalAmount = $validated['qty'] * $price->harga;

            // =========================
            // SIMPAN BUKTI TRANSFER
            // =========================
            $buktiPath = null;
            if ($request->hasFile('bukti_transfer')) {
                $buktiPath = $request->file('bukti_transfer')
                    ->store('bukti-preorder', 'public');
            }

            // =========================
            // SIMPAN PREORDER
            // status default = pending (DB)
            // =========================
            $preorder = Preorder::create([
                'user_id'           => $user->id,
                'price_id'          => $price->id,
                'qty'               => $validated['qty'],
                'total_amount'      => $totalAmount,
                'tanggal_preorder'  => Carbon::now()->toDateString(),
                'deskripsi'         => $validated['deskripsi'] ?? null,
                'bukti_transfer'    => $buktiPath,
                'metode_pembayaran' => $validated['metode_pembayaran'],
            ]);

            // =========================
            // NOTIFIKASI WHATSAPP ADMIN
            // =========================
            $adminNumber = "6285165755238";

            $message = "📌 *PRE-ORDER BARU* 📌\n\n"
                . "*Nama:* {$user->name}\n"
                . "*Email:* {$user->email}\n"
                . "*No Telp:* {$user->no_telp}\n"
                . "*Alamat:* {$user->alamat}\n\n"
                . "=====================\n"
                . "*DETAIL PREORDER*\n"
                . "=====================\n"
                . "*Produk:* {$price->product->nama_produk}\n"
                . "*Variasi:* {$price->variasi}\n"
                . "*Qty:* {$preorder->qty}\n"
                . "*Total:* Rp " . number_format($totalAmount, 0, ',', '.') . "\n"
                . "*Metode:* " . strtoupper($validated['metode_pembayaran']) . "\n"
                . "*Tanggal:* {$preorder->tanggal_preorder}\n"
                . "*Catatan:* {$preorder->deskripsi}\n"
                . "*Status:* Pending\n\n"
                . "Silakan cek di admin panel 🙏";

            return redirect(
                "https://wa.me/{$adminNumber}?text=" . urlencode($message)
            );

        } catch (\Exception $e) {

            Log::error('Preorder Store Error', [
                'message' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return back()->with('error', 'Gagal membuat pre-order. Silakan coba lagi.');
        }
    }
    
}
