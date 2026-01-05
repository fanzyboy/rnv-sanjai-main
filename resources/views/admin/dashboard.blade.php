@extends('layouts.template')

@section('title', 'Dashboard Admin')

@section('content')
<style>
    /* Tambahan style khusus dashboard agar lebih cantik */
    .stat-card {
        border: none;
        border-radius: 15px;
        transition: transform 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .icon-shape {
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.5rem;
    }
    .card-orders { background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); color: white; }
    .card-revenue { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; }
    .card-pending { background: linear-gradient(135deg, #f09819 0%, #edde5d 100%); color: white; }
    .card-best { background: linear-gradient(135deg, #ff512f 0%, #dd2476 100%); color: white; }

    .table thead th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: #7d7d7d;
        border: none;
    }

    @media (max-width: 576px) {
        .stat-card h5 { font-size: 0.85rem; }
        .stat-card h2, .stat-card h4 { font-size: 1.25rem; }
        .icon-shape { width: 35px; height: 35px; font-size: 1rem; }
    }
</style>

<div class="container-fluid py-3">

    {{-- Header Dashboard --}}
    <div class="mb-4">
        <h4 class="fw-bold">Ringkasan Statistik</h4>
        <p class="text-muted small">Pantau performa bisnis R&V Sanjai hari ini.</p>
    </div>

    {{-- Card Statistik --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-md-3">
            <div class="card stat-card card-orders shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-shape">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                    </div>
                    <h5 class="mb-1 opacity-75">Total Pesanan</h5>
                    <h2 class="fw-bold mb-0">{{ $totalOrders }}</h2>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card card-revenue shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-shape">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                    <h5 class="mb-1 opacity-75">Pendapatan</h5>
                    <h2 class="fw-bold mb-0" style="font-size: 1.1rem;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card card-pending shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-shape">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <h5 class="mb-1 opacity-75">Menunggu</h5>
                    <h2 class="fw-bold mb-0">{{ $pendingOrders }}</h2>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card card-best shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-shape">
                            <i class="fas fa-crown"></i>
                        </div>
                    </div>
                    <h5 class="mb-1 opacity-75">Terlaris</h5>
                    <h4 class="fw-bold mb-0 text-truncate">
                       {{ $bestProduct && $bestProduct->product ? $bestProduct->product->nama_produk : 'N/A' }}
                    </h4>
                    @if($bestProduct)
                        <small class="opacity-75">{{ $bestProduct->total_sold }} terjual</small>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- Pesanan terbaru --}}
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center" style="border-radius: 15px 15px 0 0;">
            <h5 class="mb-0 fw-bold"><i class="fas fa-history me-2 text-primary"></i>Pesanan Terbaru</h5>
            <a href="{{ route('admin.transactions') }}" class="btn btn-sm btn-outline-primary rounded-pill">Lihat Semua</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Pemesan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestOrders as $order)
                        <tr>
                            <td class="ps-4 text-muted">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-bold">{{ $order->user->name }}</div>
                            </td>
                            <td class="fw-semibold text-dark">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2
                                    @if($order->status == 'pending') bg-warning text-dark
                                    @elseif($order->status == 'proses') bg-primary
                                    @elseif($order->status == 'selesai') bg-success
                                    @else bg-secondary
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="text-muted" style="font-size: 0.85rem;">
                                {{ $order->created_at->format('d M Y') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <img src="https://illustrations.popsy.co/gray/box.svg" alt="empty" style="width: 100px;" class="mb-3">
                                <p class="text-muted">Belum ada pesanan masuk</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
