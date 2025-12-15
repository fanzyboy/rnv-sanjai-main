@extends('layouts.main')

@section('content')
<div class="container py-5">

    {{-- ================= HEADER ================= --}}
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-dark">
                <span class="text-orange">🛍️</span> Pesanan Saya
            </h2>
            <p class="text-muted">Kelola dan pantau status pesanan Anda</p>
        </div>
    </div>

    {{-- ================ KOSONG ================= --}}
    @if($orders->isEmpty() && $preorders->isEmpty())
        <div class="text-center py-5">
            <h5 class="fw-bold">Belum Ada Pesanan</h5>
        </div>
    @endif

    {{-- ================ ORDER ================= --}}
    @if(!$orders->isEmpty())
    <h4 class="fw-bold mb-3 mt-4">📦 Order</h4>

    @foreach ($orders as $order)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <h5 class="fw-bold text-orange">#{{ $order->id }}</h5>
                    <small class="text-muted">
                        {{ $order->created_at->format('d M Y H:i') }}
                    </small>
                </div>

                <span class="badge
                    @if($order->status == 'pending') badge-pending
                    @elseif($order->status == 'proses') badge-proses
                    @elseif($order->status == 'dikirim') badge-kirim
                    @elseif($order->status == 'selesai') badge-selesai
                    @else badge-batal
                    @endif">
                    {{ ucfirst($order->status) }}
                </span>
            </div>

            {{-- ITEMS --}}
            <div class="border-top border-bottom py-3 my-3">

            @foreach($order->items as $item)
            <div class="mb-3">

                <strong>{{ $item->product->nama_produk }}</strong><br>
                <small>
                    Rp {{ number_format($item->price->harga,0,',','.') }}
                    × {{ $item->quantity }}
                </small>

                {{-- ============ RATING PER PRODUK ============ --}}
                @if($order->status === 'selesai')

                @php
                    $rating = $item->product->ratings
                        ->where('order_id', $order->id)
                        ->where('user_id', auth()->id())
                        ->first();
                @endphp

                <div class="mt-2">

                    {{-- SUDAH DIRATING --}}
                    @if($rating)
                        <div class="d-flex align-items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="star {{ $i <= $rating->rating ? 'filled' : '' }}">★</span>
                            @endfor
                        </div>

                        @if($rating->review)
                            <div class="text-muted small">
                                "{{ $rating->review }}"
                            </div>
                        @endif

                    {{-- FORM RATING --}}
                    @else
                        <form action="{{ route('rating.store') }}"
                              method="POST"
                              class="mt-2">

                            @csrf

                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            <input type="hidden" name="product_id" value="{{ $item->product->id }}">

                            <div class="rating-stars">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio"
                                           id="star{{ $i }}-{{ $order->id }}-{{ $item->id }}"
                                           name="rating"
                                           value="{{ $i }}"
                                           required>
                                    <label for="star{{ $i }}-{{ $order->id }}-{{ $item->id }}">★</label>
                                @endfor
                            </div>

                            <textarea name="review"
                                      class="form-control form-control-sm mt-1"
                                      rows="2"
                                      placeholder="Tulis ulasan (opsional)"></textarea>

                            <button class="btn btn-sm btn-success mt-2">
                                Kirim Rating
                            </button>
                        </form>
                    @endif

                </div>
                @endif
            </div>
            @endforeach

            </div>

            {{-- TOTAL --}}
            <div class="d-flex justify-content-between total-section">
                <span class="fw-semibold">Total Dibayar</span>
                <strong class="text-orange">
                    Rp {{ number_format($order->total_amount,0,',','.') }}
                </strong>
            </div>

        </div>
    </div>
    @endforeach
    @endif

</div>

{{-- ================= STYLE ================= --}}
<style>
.text-orange { color:#ff6b35; }
.total-section { background:#fff4f0; padding:12px; border-radius:8px; }

.badge-pending{background:#ffc107}
.badge-proses{background:#ff6b35;color:#fff}
.badge-kirim{background:#17a2b8;color:#fff}
.badge-selesai{background:#28a745;color:#fff}
.badge-batal{background:#dc3545;color:#fff}

.rating-stars {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-start;
    gap: 4px;
}

.rating-stars input {
    display: none;
}

.rating-stars label {
    font-size: 22px;
    color: #ddd;
    cursor: pointer;
    transition: 0.2s;
}

.rating-stars input:checked ~ label,
.rating-stars label:hover,
.rating-stars label:hover ~ label {
    color: #ffc107;
}

.star {
    font-size: 18px;
    color: #ddd;
}

.star.filled {
    color: #ffc107;
}
</style>
@endsection
