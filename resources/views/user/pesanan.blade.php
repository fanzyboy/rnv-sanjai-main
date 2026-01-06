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

    {{-- ======================== EMPTY STATE ======================== --}}
    @if($orders->isEmpty() && $preorders->isEmpty())
        <div class="text-center py-5 shadow-sm bg-white rounded-3">
            <div class="empty-state-icon mb-3">
                <i class="bi bi-cart-x text-orange" style="font-size: 4rem;"></i>
            </div>
            <h5 class="text-dark fw-bold">Belum Ada Pesanan</h5>
            <p class="text-muted">Kamu belum memiliki riwayat pesanan atau preorder.</p>
            <a href="{{ route('produk') }}" class="btn btn-orange px-4 fw-bold">Belanja Sekarang</a>
        </div>
    @endif

    {{-- ======================== ORDER SECTION ======================== --}}
    @if(!$orders->isEmpty())
        <h4 class="fw-bold text-dark mb-3 mt-4"><i class="bi bi-box-seam text-orange me-2"></i>Daftar Pesanan</h4>
        @foreach ($orders as $order)
            <div class="card border-0 shadow-sm mb-4 order-card">
                <div class="card-body p-4">
                    @php
                        $payment = $order->payments->first();
                        $paymentMethod = strtolower($payment->metode ?? 'cod');
                        $isTransfer = $paymentMethod == 'transfer';
                    @endphp

                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="mb-1 fw-bold text-dark">#{{ $order->id }}</h5>
                            <small class="text-muted">
                                <i class="bi bi-calendar3"></i> {{ $order->created_at->format('d M Y, H:i') }}
                                <span class="mx-2">|</span>
                                <i class="bi bi-credit-card"></i> {{ ucfirst($paymentMethod) }}
                            </small>
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

                    @if($order->status == 'ditolak' && $isTransfer && $order->bukti_admin)
                        <div class="alert alert-danger p-3 mb-3 border-danger bg-light-red">
                            <h6 class="text-danger fw-bold mb-2"><i class="bi bi-info-circle-fill"></i> Pesanan Ditolak & Sudah Direfund</h6>
                            <p class="small mb-2 text-dark">Dana sebesar <strong>Rp {{ number_format($order->refund_amount ?? $order->total_amount, 0, ',', '.') }}</strong> telah ditransfer kembali.</p>
                            <a href="{{ Storage::url($order->bukti_admin) }}" target="_blank" class="btn btn-sm btn-danger fw-bold">
                                <i class="bi bi-image me-1"></i> Lihat Bukti Refund
                            </a>
                        </div>
                    @endif

                    <div class="border-top border-bottom py-3 my-3">
                        @foreach ($order->items as $item)
                            <div class="d-flex justify-content-between align-items-center mb-3 item-row">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">{{ $item->product->nama_produk }}</h6>
                                    <div class="small text-muted">
                                        {{ $item->quantity }} pcs x Rp {{ number_format($item->price->harga, 0, ',', '.') }}
                                    </div>
                                </div>
                                @if($order->status == 'selesai')
                                    @php $sudahRating = App\Models\Rating::where('order_id', $order->id)->where('product_id', $item->product_id)->first(); @endphp
                                    <div>
                                        @if($sudahRating)
                                            <div class="text-warning small">
                                                @for($i=1; $i<=5; $i++) <i class="bi bi-star{{ $i <= $sudahRating->rating ? '-fill' : '' }}"></i> @endfor
                                            </div>
                                        @else
                                            {{-- Perbaikan Parameter di sini --}}
                                            <button class="btn btn-sm btn-orange-outline fw-bold"
                                                onclick="openRatingModal('{{ $order->id }}', '{{ $item->product_id }}', '', '{{ $item->product->nama_produk }}')">
                                                Beri Rating
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between align-items-center total-section">
                        <span class="fw-semibold">Total Pembayaran</span>
                        <h5 class="mb-0 fw-bold text-orange">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    {{-- ======================== PREORDER SECTION ======================== --}}
    @if(!$preorders->isEmpty())
        <h4 class="fw-bold text-dark mb-3 mt-5"><i class="bi bi-clipboard-check text-orange me-2"></i>Daftar Preorder</h4>
        @foreach ($preorders as $po)
            <div class="card border-0 shadow-sm mb-4 order-card">
                <div class="card-body p-4">
                    @php $isTransferPO = strtolower($po->metode_pembayaran) == 'transfer'; @endphp

                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="mb-1 fw-bold text-dark text-orange">#PO-{{ $po->id }}</h5>
                            <small class="text-muted">
                                <i class="bi bi-calendar-event"></i> {{ \Carbon\Carbon::parse($po->tanggal_preorder)->format('d M Y') }}
                                <span class="mx-2">|</span>
                                <i class="bi bi-wallet2"></i> {{ strtoupper($po->metode_pembayaran) }}
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

                    @if($po->status == 'ditolak' && $isTransferPO && $po->bukti_admin)
                        <div class="alert alert-danger p-3 mb-3 border-danger bg-light-red">
                            <h6 class="text-danger fw-bold mb-2"><i class="bi bi-info-circle-fill"></i> Preorder Ditolak & Refund Selesai</h6>
                            <p class="small mb-2 text-dark">Dana telah dikirim kembali ke rekening Anda.</p>
                            <a href="{{ Storage::url($po->bukti_admin) }}" target="_blank" class="btn btn-sm btn-danger fw-bold">
                                <i class="bi bi-image me-1"></i> Lihat Bukti Refund
                            </a>
                        </div>
                    @endif

                    <div class="border-top border-bottom py-3 my-3">
                        <div class="d-flex justify-content-between align-items-center item-row">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold">{{ $po->price->product->nama_produk }}</h6>
                                <div class="small text-muted">
                                    {{ $po->qty }} pcs | Variasi: {{ $po->price->berat ?? $po->price->variasi }}g
                                </div>
                            </div>
                            @if($po->status == 'selesai')
                                @php $sudahRatingPO = App\Models\Rating::where('preorder_id', $po->id)->first(); @endphp
                                <div>
                                    @if($sudahRatingPO)
                                        <div class="text-warning small">
                                            @for($i=1; $i<=5; $i++) <i class="bi bi-star{{ $i <= $sudahRatingPO->rating ? '-fill' : '' }}"></i> @endfor
                                        </div>
                                    @else
                                        {{-- Perbaikan Parameter di sini --}}
                                        <button class="btn btn-sm btn-orange-outline fw-bold"
                                            onclick="openRatingModal('', '{{ $po->price->product_id }}', '{{ $po->id }}', '{{ $po->price->product->nama_produk }}')">
                                            Beri Rating
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center total-section">
                        <span class="fw-semibold">Total Preorder</span>
                        <h5 class="mb-0 fw-bold text-orange">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

{{-- ======================== MODAL RATING ======================== --}}
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
                    <input type="hidden" name="preorder_id" id="modal_preorder_id">
                    <input type="hidden" name="rating" id="modal_rating_value" value="5">

                    <h5 id="modal_product_name" class="fw-bold text-dark mb-4">Nama Produk</h5>

                    <div class="star-rating-box mb-4">
                        <div class="stars-input d-flex justify-content-center gap-2">
                            @for($i=1; $i<=5; $i++)
                                <i class="bi bi-star-fill clickable-star" data-value="{{ $i }}"></i>
                            @endfor
                        </div>
                        <p class="mt-2 fw-bold text-orange" id="rating-text">Sangat Puas</p>
                    </div>

                    <div class="form-group text-start">
                        <label class="small fw-bold text-muted mb-2">Tulis ulasan Anda (Opsional)</label>
                        <textarea name="review" class="form-control" rows="3" placeholder="Bagikan pengalaman kamu..."></textarea>
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

{{-- ======================== STYLES ======================== --}}
<style>
:root { --orange-primary: #ff6b35; --orange-light: #fff4f0; --red-light: #fff5f5; }
.text-orange { color: var(--orange-primary) !important; }
.bg-orange { background-color: var(--orange-primary) !important; }
.btn-orange { background: var(--orange-primary); color: white; border: none; }
.btn-orange:hover { background: #e85a2a; color: white; transform: scale(1.02); }
.btn-orange-outline { border: 2px solid var(--orange-primary); color: var(--orange-primary); background: transparent; }
.btn-orange-outline:hover { background: var(--orange-primary); color: white; }
.order-card { border-radius: 12px; transition: 0.3s; border-left: 5px solid transparent !important; }
.order-card:hover { box-shadow: 0 10px 20px rgba(255, 107, 53, 0.1) !important; border-left: 5px solid var(--orange-primary) !important; }
.status-badge { font-size: 0.75rem; font-weight: 700; }
.badge-pending { background: #fff3cd; color: #856404; }
.badge-proses { background: #cfe2ff; color: #084298; }
.badge-kirim { background: #d1ecf1; color: #0c5460; }
.badge-selesai { background: #d4edda; color: #155724; }
.badge-batal { background: #f8d7da; color: #721c24; }
.bg-light-red { background-color: var(--red-light) !important; border: 1px solid #f5c6cb; }
.total-section { background: var(--orange-light); padding: 15px; border-radius: 10px; }
.clickable-star { font-size: 2.5rem; color: #e9ecef; cursor: pointer; transition: 0.2s; }
.clickable-star.active { color: #ffc107; }
</style>

{{-- ======================== SCRIPTS ======================== --}}
<script>
    function openRatingModal(orderId, productId, preorderId, productName) {
        // Set Value ke Hidden Input
        document.getElementById('modal_order_id').value = orderId;
        document.getElementById('modal_product_id').value = productId;
        document.getElementById('modal_preorder_id').value = preorderId;
        document.getElementById('modal_product_name').innerText = productName;

        // Reset Bintang ke 5
        updateStars(5);

        // Tampilkan Modal
        var myModal = new bootstrap.Modal(document.getElementById('ratingModal'));
        myModal.show();
    }

    const starItems = document.querySelectorAll('.clickable-star');
    const ratingTexts = { 1: "Buruk", 2: "Kurang", 3: "Cukup", 4: "Puas", 5: "Sangat Puas" };

    starItems.forEach(star => {
        star.addEventListener('click', function() {
            updateStars(this.getAttribute('data-value'));
        });
    });

    function updateStars(value) {
        document.getElementById('modal_rating_value').value = value;
        document.getElementById('rating-text').innerText = ratingTexts[value];
        starItems.forEach(s => {
            s.classList.toggle('active', s.getAttribute('data-value') <= value);
        });
    }
</script>
@endsection
