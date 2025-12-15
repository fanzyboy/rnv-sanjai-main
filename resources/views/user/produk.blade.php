@extends('layouts.main')

@section('title', 'Produk - R&V Sanjai')

@section('content')
<div class="bg-light pt-4 pb-3">
    <div class="container text-center px-3">
        <h1 class="display-6 fw-bold text-dark mb-2">Katalog Produk Kami</h1>
        <p class="lead text-muted mb-0 fs-6">
            Pilih ukuran sesuai selera dan kebutuhan kamu
        </p>
    </div>
</div>

<div class="container py-3 px-3">

    @if(session('error'))
        <div class="alert alert-danger mb-3">
            {{ session('error') }}
        </div>
    @endif

    <div class="row g-3">

        @foreach ($produk as $item)
        @php
            $avgRating = round($item->ratings->avg('rating'), 1);
            $totalRating = $item->ratings->count();
        @endphp

        <div class="col-6 col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm border-0 product-card">

                {{-- IMAGE --}}
                <div class="position-relative overflow-hidden" style="height:160px;">
                    @if ($item->jenis_produk == 'manis')
                        <span class="badge bg-warning position-absolute top-0 end-0 m-2">Manis</span>
                    @elseif($item->jenis_produk == 'pedas')
                        <span class="badge bg-danger position-absolute top-0 end-0 m-2">Pedas</span>
                    @endif

                    <img src="{{ $item->foto ? asset('storage/' . $item->foto) : asset('images/default.png') }}"
                         class="card-img-top w-100 h-100 object-fit-cover"
                         alt="{{ $item->nama_produk }}">
                </div>

                {{-- BODY --}}
                <div class="card-body d-flex flex-column p-2 p-md-3">

                    <h5 class="fw-bold text-dark mb-1 fs-6">
                        {{ $item->nama_produk }}
                    </h5>

                    {{-- RATING --}}
                    <div class="rating-box mb-1">
                        @if($totalRating > 0)
                            @for($i = 1; $i <= 5; $i++)
                                <span class="star {{ $i <= floor($avgRating) ? 'filled' : '' }}">★</span>
                            @endfor
                            <small class="text-muted">
                                {{ $avgRating }} ({{ $totalRating }})
                            </small>
                        @else
                            <small class="text-muted">Belum ada rating</small>
                        @endif
                    </div>

                    <p class="text-muted mb-2" style="font-size:0.75rem;">
                        {{ Str::limit($item->deskripsi, 40) }}
                    </p>

                    <div class="d-grid gap-1 mt-auto">
                        <a href="{{ route('produk.show', $item->id) }}"
                           class="btn btn-sm btn-warning fw-bold"
                           style="font-size:0.8rem;">
                            <i class="fas fa-shopping-cart me-1"></i>
                            Checkout
                        </a>
                    </div>

                </div>
            </div>
        </div>
        @endforeach

    </div>
</div>

{{-- ================= STYLE ================= --}}
<style>
.product-card {
    transition: transform .2s ease, box-shadow .2s ease;
}
.product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}

.rating-box {
    display: flex;
    align-items: center;
    gap: 4px;
}

.star {
    font-size: 14px;
    color: #ddd;
}

.star.filled {
    color: #ffc107;
}
</style>
@endsection
