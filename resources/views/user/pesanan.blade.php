@extends('layouts.main')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<div class="container py-5">

    {{-- ======================== HEADER ======================== --}}
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-dark">
                <span class="text-orange">🛍️</span> Pesanan Saya
            </h2>
            <p class="text-muted">Kelola dan pantau status pesanan & preorder Anda</p>
        </div>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ======================== ORDER KOSONG ======================== --}}
    @if($orders->isEmpty() && $preorders->isEmpty())
        <div class="text-center py-5">
            <div class="empty-state-icon mb-3">
                <svg width="80" height="80" fill="currentColor" class="text-orange" viewBox="0 0 16 16">
                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </svg>
            </div>
            <h5 class="text-dark fw-bold">Belum Ada Pesanan</h5>
            <p class="text-muted">Kamu belum memiliki pesanan atau preorder.</p>
        </div>
    @endif

    {{-- ======================== ORDER SECTION ======================== --}}
    @if(!$orders->isEmpty())
        <h4 class="fw-bold text-dark mb-3 mt-4">📦 Order</h4>

        @foreach ($orders as $order)
            <div class="card border-0 shadow-sm mb-3 order-card">
                <div class="card-body p-4">
                    @php
                        $payment = $order->payments->first();
                        $paymentMethod = strtolower($payment->metode ?? 'cod');
                        $isTransfer = $paymentMethod == 'transfer';
                    @endphp

                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="mb-1 fw-bold text-dark">
                                <span class="text-orange">#{{ $order->id }}</span>
                            </h5>
                            <small class="text-muted">
                                <i class="bi bi-calendar3 text-orange"></i> {{ $order->created_at->format('d M Y, H:i') }}
                            </small>
                            <span class="ms-3 small text-muted">
                                | Metode: **{{ ucfirst($paymentMethod) }}**
                            </span>
                        </div>
                        <span class="badge rounded-pill px-3 py-2 status-badge
                            @if($order->status == 'pending') badge-pending
                            @elseif($order->status == 'proses') badge-proses
                            @elseif($order->status == 'dikirim') badge-kirim
                            @elseif($order->status == 'selesai') badge-selesai
                            @elseif($order->status == 'ditolak') badge-batal
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    {{-- LOGIKA REFUND (DARI UI LAMA ANDA) --}}
                    @if($order->status == 'ditolak' && $isTransfer && $order->bukti_admin)
                        <div class="alert alert-danger p-3 mb-3 border-danger bg-light-red">
                            <h6 class="text-danger fw-bold mb-2">
                                <i class="bi bi-x-circle-fill"></i> Pesanan Ditolak & Sudah Direfund
                            </h6>
                            <p class="small mb-2">
                                Sejumlah **Rp {{ number_format($order->refund_amount ?? 0, 0, ',', '.') }}** telah dikembalikan.
                            </p>
                            <a href="{{ Storage::url($order->bukti_admin) }}" target="_blank" class="btn btn-sm btn-outline-danger mt-1">
                                <i class="bi bi-file-earmark-image"></i> Lihat Bukti Refund Admin
                            </a>
                        </div>
                    @elseif($order->status == 'ditolak' && !$isTransfer)
                        <div class="alert alert-danger p-3 mb-3 border-danger bg-light-red">
                            <h6 class="text-danger fw-bold mb-0">
                                <i class="bi bi-x-circle-fill"></i> Pesanan Ditolak (Non-Transfer)
                            </h6>
                            <p class="small mb-0">Order ini dibatalkan oleh Admin.</p>
                        </div>
                    @endif

                    {{-- ITEMS --}}
                    <div class="border-top border-bottom py-3 my-3 order-items">
                        @foreach ($order->items as $item)
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 item-row">
                                <div class="flex-grow-1">
                                    <h6 class="mb-2 fw-semibold text-dark">{{ $item->product->nama_produk }}</h6>
                                    <div class="d-flex flex-wrap gap-3 text-muted small">
                                        <span class="item-info">
                                            <i class="bi bi-tag-fill text-orange"></i>
                                            Rp {{ number_format($item->price->harga, 0, ',', '.') }}
                                        </span>
                                        @if(!empty($item->price->berat))
                                        <span class="item-info">
                                            <i class="bi bi-box-seam text-orange"></i>
                                            {{ $item->price->berat }}g
                                        </span>
                                        @endif
                                        <span class="item-info">
                                            <i class="bi bi-cart-check-fill text-orange"></i>
                                            Qty: {{ $item->quantity }}
                                        </span>
                                    </div>
                                </div>

                                {{-- LOGIKA RATING (DISEBELAH KANAN ITEM) --}}
                                @if($order->status == 'selesai')
                                    @php
                                        $sudahRating = App\Models\Rating::where('order_id', $order->id)->where('product_id', $item->product_id)->first();
                                    @endphp
                                    <div class="ms-3">
                                        @if($sudahRating)
                                            <div class="text-warning small">
                                                @for($i=1; $i<=5; $i++) <i class="bi bi-star{{ $i <= $sudahRating->rating ? '-fill' : '' }}"></i> @endfor
                                            </div>
                                        @else
                                            <button class="btn btn-sm btn-orange-outline fw-bold" onclick="openRatingModal('{{ $order->id }}', '{{ $item->product_id }}', '{{ $item->product->nama_produk }}')">
                                                Beri Rating
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between align-items-center total-section">
                        <span class="text-muted fw-semibold">Total Pembayaran</span>
                        <h5 class="mb-0 fw-bold text-orange">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </h5>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    {{-- ======================== PREORDER SECTION (DENGAN RATING) ======================== --}}
    @if(!$preorders->isEmpty())
        <h4 class="fw-bold text-dark mb-3 mt-5">📝 Preorder</h4>
        @foreach ($preorders as $po)
            <div class="card border-0 shadow-sm mb-3 order-card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="mb-1 fw-bold text-dark">
                                <span class="text-orange">#PO{{ $po->id }}</span>
                            </h5>
                            <small class="text-muted">
                                <i class="bi bi-calendar3 text-orange"></i> {{ $po->tanggal_preorder }}
                            </small>
                        </div>
                        <span class="badge rounded-pill px-3 py-2 status-badge
                            @if($po->status == 'pending') badge-pending
                            @elseif($po->status == 'proses') badge-proses
                            @elseif($po->status == 'selesai') badge-selesai
                            @elseif($po->status == 'ditolak') badge-batal
                            @endif">
                            {{ ucfirst($po->status) }}
                        </span>
                    </div>

                    <div class="border-top border-bottom py-3 my-3 order-items">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 item-row">
                            <div class="flex-grow-1">
                                <h6 class="mb-2 fw-semibold text-dark">{{ $po->price->product->nama_produk }}</h6>
                                <div class="d-flex flex-wrap gap-3 text-muted small">
                                    <span class="item-info">
                                        <i class="bi bi-tag-fill text-orange"></i>
                                        Variasi: {{ $po->price->variasi }}
                                    </span>
                                    <span class="item-info">
                                        <i class="bi bi-cart-check-fill text-orange"></i>
                                        Qty: {{ $po->qty }}
                                    </span>
                                </div>
                                @if($po->deskripsi)
                                <p class="mt-2 small text-muted">
                                    <i class="bi bi-chat-left-text text-orange"></i> {{ $po->deskripsi }}
                                </p>
                                @endif
                            </div>

                            {{-- RATING UNTUK PREORDER --}}
                            @if($po->status == 'selesai')
                                @php
                                    $sudahRatingPO = App\Models\Rating::where('order_id', $po->id)->where('product_id', $po->price->product_id)->first();
                                @endphp
                                <div class="ms-3">
                                    @if($sudahRatingPO)
                                        <div class="text-warning small">
                                            @for($i=1; $i<=5; $i++) <i class="bi bi-star{{ $i <= $sudahRatingPO->rating ? '-fill' : '' }}"></i> @endfor
                                        </div>
                                    @else
                                        <button class="btn btn-sm btn-orange-outline fw-bold" onclick="openRatingModal('{{ $po->id }}', '{{ $po->price->product_id }}', '{{ $po->price->product->nama_produk }}')">
                                            Beri Rating
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center total-section">
                        <span class="text-muted fw-semibold">Status</span>
                        <h6 class="mb-0 fw-bold text-orange">{{ ucfirst($po->status) }}</h6>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

{{-- ======================== MODAL RATING (POPUP) ======================== --}}
<div class="modal fade" id="ratingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-orange text-white">
                <h5 class="modal-title fw-bold">Ulas Produk</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('rating.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-center">
                    <input type="hidden" name="order_id" id="modal_order_id">
                    <input type="hidden" name="product_id" id="modal_product_id">
                    <input type="hidden" name="rating" id="modal_rating_value" value="5">

                    <h5 id="modal_product_name" class="fw-bold text-dark mb-4">Nama Produk</h5>

                    <div class="star-rating-box mb-4">
                        <div class="stars-input d-flex justify-content-center gap-2">
                            <i class="bi bi-star-fill clickable-star" data-value="1"></i>
                            <i class="bi bi-star-fill clickable-star" data-value="2"></i>
                            <i class="bi bi-star-fill clickable-star" data-value="3"></i>
                            <i class="bi bi-star-fill clickable-star" data-value="4"></i>
                            <i class="bi bi-star-fill clickable-star" data-value="5"></i>
                        </div>
                        <p class="mt-2 fw-bold text-orange" id="rating-text">Sangat Puas</p>
                    </div>

                    <div class="form-group text-start">
                        <label class="small fw-bold text-muted mb-2">Tulis ulasan Anda (Opsional)</label>
                        <textarea name="review" class="form-control" rows="3" placeholder="Bagikan pengalaman kamu memakai produk ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-orange fw-bold px-4">Kirim Ulasan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ======================== CSS LAMA + RATING ======================== --}}
<style>
:root {
    --orange-primary: #ff6b35;
    --orange-light: #fff4f0;
    --red-light: #f8d7da;
}

.text-orange { color: var(--orange-primary) !important; }
.bg-orange { background-color: var(--orange-primary) !important; }
.btn-orange { background: var(--orange-primary); color: white; border: none; }
.btn-orange:hover { background: #e85a2a; color: white; }

.btn-orange-outline {
    border: 2px solid var(--orange-primary);
    color: var(--orange-primary);
    background: transparent;
    transition: 0.3s;
}
.btn-orange-outline:hover { background: var(--orange-primary); color: white; }

/* CSS BINTANG POPUP */
.clickable-star {
    font-size: 2.8rem;
    color: #dee2e6;
    cursor: pointer;
    transition: transform 0.2s, color 0.2s;
}
.clickable-star.active { color: #ffc107 !important; }
.clickable-star:hover { transform: scale(1.1); }

.order-card {
    transition: all 0.3s ease;
    border-left: 4px solid transparent !important;
}
.order-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 .75rem 2rem rgba(255, 107, 53, 0.15) !important;
    border-left: 4px solid var(--orange-primary) !important;
}

.status-badge { font-size: 0.8rem; font-weight: 600; letter-spacing: 0.3px; }
.badge-pending { background: #ffc107; color:#000; }
.badge-proses { background: #ff6b35; color:#fff; }
.badge-kirim { background: #17a2b8; color:#fff; }
.badge-selesai { background: #28a745; color:#fff; }
.badge-batal { background: #dc3545; color:#fff; }

.bg-light-red { background-color: var(--red-light) !important; }
.item-row { border-bottom: 1px dashed #e9ecef; }
.item-row:last-child { border-bottom: none !important; }
.item-info { display: inline-flex; align-items: center; gap: 5px; }
.total-section { background: var(--orange-light); padding: 15px; border-radius: 8px; margin-top: 10px; }

.empty-state-icon { animation: float 3s ease-in-out infinite; }
@keyframes float {
    0%,100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
</style>

{{-- ======================== JS RATING ======================== --}}
<script>
    function openRatingModal(orderId, productId, productName) {
        document.getElementById('modal_order_id').value = orderId;
        document.getElementById('modal_product_id').value = productId;
        document.getElementById('modal_product_name').innerText = productName;
        updateStars(5);
        new bootstrap.Modal(document.getElementById('ratingModal')).show();
    }

    const starItems = document.querySelectorAll('.clickable-star');
    const ratingTexts = { 1: "Buruk", 2: "Kurang", 3: "Cukup", 4: "Puas", 5: "Sangat Puas" };

    starItems.forEach(star => {
        star.addEventListener('click', function() {
            updateStars(this.getAttribute('data-value'));
        });
        star.addEventListener('mouseover', function() {
            highlightStars(this.getAttribute('data-value'));
        });
        star.addEventListener('mouseout', function() {
            highlightStars(document.getElementById('modal_rating_value').value);
        });
    });

    function highlightStars(value) {
        starItems.forEach(s => {
            s.classList.toggle('active', s.getAttribute('data-value') <= value);
        });
    }

    function updateStars(value) {
        document.getElementById('modal_rating_value').value = value;
        document.getElementById('rating-text').innerText = ratingTexts[value];
        highlightStars(value);
    }
</script>
@endsection
