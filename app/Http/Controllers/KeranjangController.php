<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\Keranjang; // Pastikan model ini sudah diimport

class KeranjangController extends Controller
{
    // TAMPILKAN ISI KERANJANG
    public function index()
    {
        if (!Auth::check()) return redirect()->route('login');

        // Ambil data dari database berdasarkan user yang login
        $items = Keranjang::with(['produk', 'variasi'])
                  ->where('user_id', Auth::id())
                  ->get();

        $keranjang = [];
        foreach ($items as $item) {
            // Sinkronisasi: jika produk/variasi dihapus admin, hapus dari keranjang user
            if (!$item->produk || !$item->variasi) {
                $item->delete();
                continue;
            }

            $keranjang[] = [
                'id'         => $item->id, // ID primary key dari tabel keranjangs
                'produk_id'  => $item->produk_id,
                'variasi_id' => $item->variasi_id,
                'produk'     => $item->produk->nama_produk,
                'gram'       => $item->variasi->berat . ' gram',
                'harga'      => $item->variasi->harga,
                'qty'        => $item->qty,
                'total'      => $item->qty * $item->variasi->harga,
                'foto'       => $item->produk->foto,
                'stok'       => $item->variasi->stok,
            ];
        }

        $total = array_sum(array_column($keranjang, 'total'));

        return view('user.keranjang', compact('keranjang', 'total'));
    }

    // Mengambil jumlah item unik untuk badge navbar
    public function getCartCount(Request $request)
    {
        $count = 0;
        if (Auth::check()) {
            $count = Keranjang::where('user_id', Auth::id())->count();
        }

        return response()->json([
            'count' => $count
        ]);
    }

    // TAMBAH PRODUK KE KERANJANG
    public function store(Request $request)
    {
        try {
            // 1. Cek Login
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu.',
                    'redirect' => route('login')
                ], 401);
            }

            // 2. Cek Role
            if (Auth::user()->role !== 'user') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pembeli yang dapat menambah keranjang.'
                ], 403);
            }

            // 3. Ambil data produk & variasi
            $produk = Product::with('prices')->findOrFail($request->produk_id);
            $variasi = $produk->prices()->findOrFail($request->variasi_id);
            $qtyInput = $request->qty ?? 1;

            // Logika Database: Cek apakah item sudah ada di keranjang user
            $item = Keranjang::where('user_id', Auth::id())
                             ->where('variasi_id', $variasi->id)
                             ->first();

            if ($item) {
                // Update Qty jika sudah ada
                $item->update([
                    'qty' => $item->qty + $qtyInput
                ]);
            } else {
                // Simpan data baru ke database
                Keranjang::create([
                    'user_id'    => Auth::id(),
                    'produk_id'  => $produk->id,
                    'variasi_id' => $variasi->id,
                    'qty'        => $qtyInput
                ]);
            }

            // Hitung total item unik di DB
            $cartCount = Keranjang::where('user_id', Auth::id())->count();

            // Berikan pesan berbeda jika stok kosong
            $pesan = ($variasi->stok <= 0)
                ? 'Produk kosong ditambahkan ke keranjang (tidak dapat di-checkout).'
                : 'Berhasil ditambahkan ke keranjang!';

            return response()->json([
                'success' => true,
                'message' => $pesan,
                'count'   => $cartCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // HAPUS ITEM KERANJANG
    public function remove($id)
    {
        // Hapus berdasarkan ID primary key database
        $item = Keranjang::where('id', $id)
                         ->where('user_id', Auth::id())
                         ->first();

        if ($item) {
            $item->delete();
            return redirect()->route('keranjang.index')->with('success', 'Item berhasil dihapus!');
        }

        return redirect()->route('keranjang.index')->with('error', 'Item tidak ditemukan!');
    }
}
