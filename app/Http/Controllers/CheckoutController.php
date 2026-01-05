<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Preorder;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Keranjang; // Tambahkan Model Keranjang
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    // ==============================
    // HALAMAN CHECKOUT
    // ==============================
    public function proses(Request $request)
    {
        // 1. MODE BELI SEKARANG (Langsung dari tombol Beli di halaman Detail Produk)
        if ($request->has(['produk_id', 'variasi_id', 'qty'])) {
            $produk = Product::find($request->produk_id);
            if (!$produk) abort(404);

            $variasi = ProductPrice::where('product_id', $request->produk_id)
                                    ->where('id', $request->variasi_id)
                                    ->first();
            if (!$variasi) abort(404);

            $qty = (int) $request->qty;

            $itemsForCheckout = [[
                'id'         => null, // Bukan dari tabel keranjang
                'produk'     => $produk->nama_produk,
                'produk_id'  => $produk->id,
                'variasi_id' => $variasi->id,
                'gram'       => $variasi->berat . ' gram',
                'harga'      => $variasi->harga,
                'qty'        => $qty,
                'total'      => $variasi->harga * $qty,
            ]];

            // Simpan ke session untuk diproses di method prosesCheckout
            session(['checkout_now' => $itemsForCheckout]);
            return view('user.checkout', ['keranjang' => $itemsForCheckout]);
        }

        // 2. MODE CHECKOUT DARI KERANJANG (Database)
        // Ambil ID keranjang yang dicentang dari form keranjang
        $selectedIds = $request->input('selected_items');

        if (!$selectedIds || !is_array($selectedIds)) {
            return redirect()->route('keranjang.index')->with('error', 'Pilih minimal satu produk untuk checkout.');
        }

        // Ambil data dari database berdasarkan ID yang dipilih
        $cartItems = Keranjang::with(['produk', 'variasi'])
            ->whereIn('id', $selectedIds)
            ->where('user_id', Auth::id())
            ->get();

        $itemsForCheckout = [];
        foreach ($cartItems as $item) {
            if ($item->produk && $item->variasi) {
                $itemsForCheckout[] = [
                    'id'         => $item->id, // ID row keranjang
                    'produk_id'  => $item->produk_id,
                    'variasi_id' => $item->variasi_id,
                    'produk'     => $item->produk->nama_produk,
                    'gram'       => $item->variasi->berat . ' gram',
                    'harga'      => $item->variasi->harga,
                    'qty'        => $item->qty,
                    'total'      => $item->qty * $item->variasi->harga,
                ];
            }
        }

        if (empty($itemsForCheckout)) {
            return redirect()->route('keranjang.index')->with('error', 'Produk tidak ditemukan di keranjang.');
        }

        // Simpan ke session khusus checkout agar saat submit datanya valid
        session(['checkout_now' => $itemsForCheckout]);

        return view('user.checkout', ['keranjang' => $itemsForCheckout]);
    }

    // ==============================
    // SIMPAN TRANSAKSI KE DATABASE
    // ==============================
    public function prosesCheckout(Request $request)
    {
        $request->validate([
            'nama'           => 'required|string|max:100',
            'alamat'         => 'required|string|max:255',
            'telepon'        => 'required|string|max:20',
            'metode'         => 'required|string',
            'bukti'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nama_bank'      => 'nullable|string|max:50',
            'nomor_rekening' => 'nullable|string|max:50',
        ]);

        $checkoutItems = session('checkout_now', []);

        if (empty($checkoutItems)) {
            return redirect()->route('keranjang.index')->with('error', 'Sesi checkout berakhir atau keranjang kosong.');
        }

        DB::beginTransaction();
        try {
            $total = array_sum(array_column($checkoutItems, 'total'));

            // 1. Buat Order
            $order = Order::create([
                'user_id'        => Auth::id(),
                'total_amount'   => $total,
                'status'         => 'pending',
                'alamat'         => $request->alamat,
                'no_hp'          => $request->telepon,
                'nama_penerima'  => $request->nama,
                'nama_bank'      => $request->nama_bank, // Menyimpan input nama_bank ke tabel orders
                'nomor_rekening' => $request->nomor_rekening, // Menyimpan input nomor_rekening ke tabel orders
            ]);

            $idsToDelete = [];

            // 2. Proses Item & Stok
            foreach ($checkoutItems as $item) {
                // Lock row variasi agar stok tidak bentrok (Race Condition)
                $variasi = ProductPrice::where('id', $item['variasi_id'])->lockForUpdate()->first();

                if (!$variasi || $variasi->stok < $item['qty']) {
                    throw new \Exception("Stok produk '{$item['produk']}' tidak mencukupi.");
                }

                // Kurangi Stok
                $variasi->decrement('stok', $item['qty']);

                // Simpan Detail Item Order
                OrderItem::create([
                    'order_id'         => $order->id,
                    'product_id'       => $item['produk_id'],
                    'product_price_id' => $item['variasi_id'],
                    'quantity'         => $item['qty'],
                    'price'            => $item['harga'], // Simpan harga saat dibeli
                ]);

                // Kumpulkan ID keranjang untuk dihapus nanti
                if ($item['id'] !== null) {
                    $idsToDelete[] = $item['id'];
                }
            }

            // 3. Simpan Bukti Pembayaran
            $buktiPath = null;
            if ($request->hasFile('bukti')) {
                $buktiPath = $request->file('bukti')->store('bukti_pembayaran', 'public');
            }

            Payment::create([
                'order_id' => $order->id,
                'metode'   => $request->metode,
                'status'   => 'pending',
                'bukti'    => $buktiPath,
            ]);

            // 4. Hapus item dari tabel keranjang di Database
            if (!empty($idsToDelete)) {
                Keranjang::whereIn('id', $idsToDelete)->delete();
            }

            // Hapus session checkout
            session()->forget('checkout_now');

            DB::commit();

            // Kirim Konfirmasi ke WhatsApp
            return $this->sendWhatsApp($order, $checkoutItems, $request, $total);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    private function sendWhatsApp($order, $items, $request, $total)
    {
        $pesan = "Halo R&V Sanjai, saya ingin konfirmasi pesanan:\n\n";
        $pesan .= "Order ID: #{$order->id}\n";
        $pesan .= "----------------------------\n";
        foreach ($items as $item) {
            $pesan .= "- {$item['produk']} ({$item['gram']}) x {$item['qty']}\n";
        }
        $pesan .= "----------------------------\n";
        $pesan .= "Total: Rp " . number_format($total, 0, ',', '.') . "\n";
        $pesan .= "Metode: " . strtoupper($request->metode) . "\n\n";
        $pesan .= "Detail Pengiriman:\n";
        $pesan .= "Nama: {$request->nama}\n";
        $pesan .= "Alamat: {$request->alamat}\n";
        $pesan .= "HP: {$request->telepon}\n";

        if ($request->nama_bank) {
            $pesan .= "Bank: {$request->nama_bank} ({$request->nomor_rekening})\n";
        }

        return redirect("https://wa.me/6285165755238?text=" . urlencode($pesan));
    }

    public function pesananSaya()
    {
        // Pastikan relasi di model Order sudah benar (items, items.product, items.price)
        $orders = Order::with(['items.product', 'items.price'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $preorders = Preorder::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('user.pesanan', compact('orders', 'preorders'));
    }
}
