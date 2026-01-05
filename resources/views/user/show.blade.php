@extends('layouts.main')

@section('title', $product->nama_produk)

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<div class="container my-5 fade-in">
    {{-- ======================== DETAIL PRODUK ======================== --}}
    <div class="card shadow-lg border-0 mb-5">
        <div class="card-body p-md-5">
            <div class="row g-4">

                {{-- GAMBAR PRODUK --}}
                <div class="col-md-5 d-flex justify-content-center align-items-center">
                    @if($product->foto)
                        <img src="{{ asset('storage/' . $product->foto) }}"
                             class="img-fluid rounded product-image-detail">
                    @else
                        <img src="{{ asset('images/no-image.png') }}"
                             class="img-fluid rounded">
                    @endif
                </div>

                <div class="col-md-7">
                    <h1 class="fw-bolder mb-1 text-rvsanjai-primary">
                        {{ $product->nama_produk }}
                    </h1>

                    {{-- RINGKASAN RATING --}}
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <div class="text-warning">
                            @php $avgRating = $product->ratings->avg('rating') ?? 0; @endphp
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $avgRating ? '-fill' : ($i - 0.5 <= $avgRating ? '-half' : '') }}"></i>
                            @endfor
                        </div>
                        <span class="text-muted small">({{ $product->ratings->count() }} Ulasan)</span>
                    </div>

                    <p class="text-muted border-bottom pb-3">
                        {{ $product->deskripsi }}
                    </p>

                    <p>
                        <strong>
                            <i class="fas fa-tags me-1"></i> Jenis:
                        </strong>
                        {{ ucfirst($product->jenis_produk) }}
                    </p>

                    {{-- VARIAN --}}
                    <h5 class="mb-2 fw-semibold">Pilih Varian:</h5>
                    <select id="price-selector" class="form-select mb-3">
                        @foreach ($product->prices as $p)
                            <option value="{{ $p->id }}"
                                    data-stok="{{ $p->stok }}"
                                    @selected($loop->first)>
                                {{ $p->berat }} gram - Rp {{ number_format($p->harga) }}
                            </option>
                        @endforeach
                    </select>

                    {{-- STOK --}}
                    <p>
                        <strong id="stok-display" class="text-secondary">
                            <i class="fas fa-spinner fa-spin"></i> Memuat stok...
                        </strong>
                    </p>

                    {{-- QTY --}}
                    <label class="fw-semibold">Jumlah:</label>
                    <input type="number" id="qty-input"
                           class="form-control mb-3"
                           value="1" min="1"
                           style="max-width:150px">

                    {{-- TAMBAH KERANJANG (AJAX FORM) --}}
                    <form id="add-to-cart-form">
                        @csrf
                        <input type="hidden" name="produk_id" value="{{ $product->id }}">
                        <input type="hidden" name="variasi_id" id="cart-variasi">
                        <input type="hidden" name="qty" id="cart-qty">

                        <button type="submit" id="btn-add-cart"
                                class="btn btn-lg w-100 btn-pesan-sekarang mb-2">
                            <i class="fas fa-shopping-cart me-2"></i>
                            <span id="btn-text">Tambah ke Keranjang</span>
                        </button>
                    </form>

                    {{-- BELI SEKARANG / PRE ORDER --}}
                    <div id="buy-action-container"></div>

                    {{-- KEMBALI --}}
                    <a href="{{ route('produk') }}"
                       class="btn btn-outline-secondary w-100 mt-3">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================== SECTION RATING & REVIEW ======================== --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h4 class="fw-bold mb-0">Ulasan Pelanggan</h4>
        </div>
        <div class="card-body p-4">
            @if($product->ratings->isEmpty())
                <div class="text-center py-4">
                    <i class="bi bi-chat-left-dots text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">Belum ada ulasan untuk produk ini.</p>
                </div>
            @else
                <div class="row mb-4 border-bottom pb-4">
                    <div class="col-md-4 text-center border-end">
                        <h1 class="display-4 fw-bold text-rvsanjai-primary mb-0">{{ number_format($avgRating, 1) }}</h1>
                        <div class="text-warning mb-2" style="font-size: 1.2rem;">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $avgRating ? '-fill' : ($i - 0.5 <= $avgRating ? '-half' : '') }}"></i>
                            @endfor
                        </div>
                        <p class="text-muted small">Berdasarkan {{ $product->ratings->count() }} penilaian</p>
                    </div>
                    <div class="col-md-8 ps-md-5 d-flex flex-column justify-content-center">
                        @for($star = 5; $star >= 1; $star--)
                            @php
                                $count = $product->ratings->where('rating', $star)->count();
                                $percent = $product->ratings->count() > 0 ? ($count / $product->ratings->count()) * 100 : 0;
                            @endphp
                            <div class="d-flex align-items-center mb-1">
                                <span class="small me-2" style="width: 20px;">{{ $star }}</span>
                                <i class="bi bi-star-fill text-warning small me-2"></i>
                                <div class="progress flex-grow-1" style="height: 8px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percent }}%"></div>
                                </div>
                                <span class="small ms-2 text-muted" style="width: 30px;">{{ $count }}</span>
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="review-list">
                    @foreach($product->ratings as $rating)
                        <div class="review-item mb-4 pb-4 border-bottom">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-bold mb-0">{{ $rating->user->name ?? 'Pembeli' }}</h6>
                                    <div class="text-warning small">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= $rating->rating ? '-fill' : '' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <small class="text-muted">{{ $rating->created_at->format('d M Y') }}</small>
                            </div>
                            <p class="text-secondary mb-0">
                                {{ $rating->review ?: 'Tidak ada komentar.' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.product-image-detail { border:5px solid #ff6b35; border-radius:15px; box-shadow:0 10px 20px rgba(0,0,0,0.15); }
.text-rvsanjai-primary { color:#ff6b35; }
.btn-pesan-sekarang { background:#ff6b35; color:white; border:none; }
.btn-pesan-sekarang:hover { background:#e55a2b; color:white; }
.btn-pre-order { background:#007bff; color:white; }
.btn-pre-order:hover { background:#0056b3; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const select = document.getElementById('price-selector');
    const qtyInput = document.getElementById('qty-input');
    const stokDisplay = document.getElementById('stok-display');
    const cartVariasi = document.getElementById('cart-variasi');
    const cartQty = document.getElementById('cart-qty');
    const buyContainer = document.getElementById('buy-action-container');
    const cartForm = document.getElementById('add-to-cart-form');
    const btnAddCart = document.getElementById('btn-add-cart');
    const btnText = document.getElementById('btn-text');

    const checkoutUrl = "{{ route('checkout.proses') }}";
    const preorderUrl = "{{ route('preorder.create') }}";

    function updateUI() {
        const opt = select.options[select.selectedIndex];
        if(!opt) return;

        const stok = Number(opt.dataset.stok);
        const variasiId = opt.value;
        const qty = parseInt(qtyInput.value) || 1;

        // Reset UI Keranjang
        btnAddCart.disabled = false;
        btnAddCart.classList.remove('btn-secondary');
        btnAddCart.classList.add('btn-pesan-sekarang');

        if (stok > 0) {
            stokDisplay.className = "text-success fw-semibold";
            stokDisplay.innerHTML = `<i class="bi bi-check-circle-fill"></i> Stok tersedia (${stok})`;

            // Validasi jika qty melebihi stok
            if (qty > stok) {
                stokDisplay.className = "text-danger fw-semibold";
                stokDisplay.innerHTML = `<i class="bi bi-exclamation-triangle-fill"></i> Jumlah melebihi stok (${stok})`;
                btnAddCart.disabled = true; // Disable tombol keranjang
            }
        } else {
            stokDisplay.className = "text-primary fw-semibold";
            stokDisplay.innerHTML = `<i class="bi bi-info-circle-fill"></i> Pre Order`;
        }

        cartVariasi.value = variasiId;
        cartQty.value = qty;

        buyContainer.innerHTML = '';
        if (stok > 0) {
            // Cek apakah tombol Beli Sekarang harus mati atau hidup
            const isDisabled = qty > stok ? 'disabled' : '';
            const btnClass = qty > stok ? 'btn-secondary' : 'btn-danger';

            buyContainer.innerHTML = `
                <button type="button"
                        onclick="handleBuyNow(${stok}, ${variasiId}, ${qty})"
                        class="btn btn-lg w-100 ${btnClass}" ${isDisabled}>
                    <i class="fas fa-bolt me-2"></i> Beli Sekarang
                </button>
            `;
        } else {
            buyContainer.innerHTML = `
                <a href="${preorderUrl}?produk_id={{ $product->id }}&variasi_id=${variasiId}&qty=${qty}"
                   class="btn btn-lg btn-primary w-100 btn-pre-order">
                    <i class="fas fa-box me-2"></i> Pre Order
                </a>
            `;
        }
    }

    // Fungsi global agar bisa dipanggil dari onclick button dinamis
    window.handleBuyNow = function(stok, variasiId, qty) {
        if(qty > stok) {
            alert('Maaf, jumlah pilihan melebihi stok yang tersedia.');
            return;
        }
        window.location.href = `${checkoutUrl}?buy_now=1&produk_id={{ $product->id }}&variasi_id=${variasiId}&qty=${qty}`;
    }

    // --- FUNGSI AJAX TAMBAH KERANJANG ---
    cartForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const opt = select.options[select.selectedIndex];
        const stok = Number(opt.dataset.stok);
        const currentQty = parseInt(qtyInput.value);

        // Validasi Akhir sebelum Fetch
        if (stok > 0 && currentQty > stok) {
            alert('Gagal: Jumlah melebihi stok tersedia.');
            return;
        }

        const formData = new FormData(this);
        btnAddCart.disabled = true;
        const originalText = btnText.innerHTML;
        btnText.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menambahkan...';

        fetch("{{ route('keranjang.tambah') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        })
        .then(response => {
            if (response.status === 401) {
                alert('Silakan login terlebih dahulu.');
                window.location.href = "{{ route('login') }}";
                return;
            }
            return response.json();
        })
        .then(data => {
            if(data && data.success) {
                const cartEvent = new CustomEvent('cartUpdated', {
                    detail: { count: data.count }
                });
                document.dispatchEvent(cartEvent);
                alert(data.message || 'Produk berhasil ditambahkan!');
            } else if (data) {
                alert(data.message || 'Gagal menambah produk.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan koneksi.');
        })
        .finally(() => {
            btnAddCart.disabled = false;
            btnText.innerHTML = originalText;
            updateUI(); // Segarkan UI
        });
    });

    qtyInput.addEventListener('input', updateUI);
    select.addEventListener('change', updateUI);

    updateUI();
});
</script>
@endsection
