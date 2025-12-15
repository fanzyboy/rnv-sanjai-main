@extends('layouts.template')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="container py-4">

    {{-- Card Statistik --}}
    <div class="row mb-4">

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5>Total Pendapatan</h5>
                    <h2 class="fw-bold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5>Total Pesanan</h5>
                    <h2 class="fw-bold">{{ $totalOrders }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5>Produk Terjual</h5>
                    <h2 class="fw-bold">{{ $totalSold }}</h2>
                </div>
            </div>
        </div>

    </div>

    {{-- Filter Laporan --}}
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="fw-semibold">Dari Tanggal</label>
            <input type="date" name="from_date" class="form-control"
                   value="{{ request('from_date') }}">
        </div>
    
        <div class="col-md-4">
            <label class="fw-semibold">Sampai Tanggal</label>
            <input type="date" name="to_date" class="form-control"
                   value="{{ request('to_date') }}">
        </div>
    
        <div class="col-md-4 d-flex gap-2">
            <button class="btn btn-primary w-100">
                <i class="fas fa-filter me-1"></i> Filter
            </button>
        </div>
    </form>
    

    {{-- Tabel Laporan --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h5 class="mb-0">Data Laporan Penjualan</h5>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode Pesanan</th>
                        <th>Nama Pemesan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>#ORD{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $order->user->name ?? '-' }}</td>
                        <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge
                                @if($order->status == 'pending') bg-warning
                                @elseif($order->status == 'proses') bg-primary
                                @elseif($order->status == 'selesai') bg-success
                                @else bg-secondary
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            Belum ada data laporan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
