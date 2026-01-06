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

        // Ambil data user yang sedang login untuk mengisi otomatis di form
        $user = Auth::user();

        // Pastikan path view sesuai (tadi di kodingan sebelumnya 'preorder.create')
        return view('preorder.create', compact('price', 'qty', 'user'));
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
            'nama_bank'         => 'nullable|string|max:50',
            'nomor_rekening'    => 'nullable|string|max:30',
        ]);

        // Logika: Jika transfer, bukti transfer wajib ada
        if ($validated['metode_pembayaran'] === 'transfer' && !$request->hasFile('bukti_transfer')) {
            return back()->withErrors([
                'bukti_transfer' => 'Bukti transfer wajib diupload untuk metode pembayaran Transfer Bank.'
            ])->withInput();
        }

        $user = Auth::user();

        // ============================================================
        // UPDATE DATA USER (SIMPAN NOMOR REKENING & BANK KE PROFIL)
        // ============================================================
        // Ini agar saat order berikutnya, data sudah otomatis terisi
        if ($request->filled('nama_bank') || $request->filled('nomor_rekening')) {
            $user->update([
                'nama_bank'      => $request->nama_bank,
                'nomor_rekening' => $request->nomor_rekening,
            ]);
        }

        try {
            $price = ProductPrice::with('product')->findOrFail($validated['price_id']);

            // Hitung Total
            $totalAmount = $validated['qty'] * $price->harga;

            // Simpan File Bukti Transfer
            $buktiPath = null;
            if ($request->hasFile('bukti_transfer')) {
                $buktiPath = $request->file('bukti_transfer')->store('bukti-preorder', 'public');
            }

            // =========================
            // SIMPAN DATA KE TABEL PREORDER
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
                // Opsional: Simpan juga ke tabel preorder jika tabelnya punya kolom ini
                'nama_bank'         => $request->nama_bank,
                'nomor_rekening'    => $request->nomor_rekening,
            ]);

            // =========================
            // NOTIFIKASI WHATSAPP ADMIN
            // =========================
            $adminNumber = "6285165755238";

            $message = "📌 *PRE-ORDER BARU* 📌\n\n"
                . "*Nama:* {$user->name}\n"
                . "*Bank:* " . ($request->nama_bank ?? '-') . "\n"
                . "*No. Rek:* " . ($request->nomor_rekening ?? '-') . "\n"
                . "*No Telp:* {$user->no_telp}\n"
                . "*Alamat:* {$user->alamat}\n\n"
                . "=====================\n"
                . "*DETAIL PREORDER*\n"
                . "=====================\n"
                . "*Produk:* {$price->product->nama_produk}\n"
                . "*Variasi:* {$price->berat} gr\n"
                . "*Qty:* {$preorder->qty} pcs\n"
                . "*Total:* Rp " . number_format($totalAmount, 0, ',', '.') . "\n"
                . "*Metode:* " . strtoupper($validated['metode_pembayaran']) . "\n"
                . "*Catatan:* " . ($preorder->deskripsi ?? '-') . "\n\n"
                . "Silakan cek admin panel untuk verifikasi bukti transfer.";

            return redirect(
                "https://wa.me/{$adminNumber}?text=" . urlencode($message)
            );

        } catch (\Exception $e) {
            Log::error('Preorder Store Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat pre-order. Silakan coba lagi.')->withInput();
        }
    }
}
