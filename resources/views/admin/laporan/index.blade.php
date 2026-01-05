@extends('layouts.template')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="container py-4">

    {{-- ===================== --}}
    {{-- STATISTIK --}}
    {{-- ===================== --}}
    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6>Total Pendapatan</h6>
                    <h4 class="fw-bold text-success">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6>Total Pesanan</h6>
                    <h4 class="fw-bold">{{ $totalOrders }}</h4>
                    <small>
                        Orders: {{ $totalOrderCount }} |
                        Preorders: {{ $totalPreorderCount }}
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6>Produk Terjual</h6>
                    <h4 class="fw-bold">{{ $totalSold }}</h4>
                </div>
            </div>
        </div>

    </div>

    {{-- ===================== --}}
    {{-- FILTER BULAN --}}
    {{-- ===================== --}}
    <form method="GET" class="row g-3 mb-4">

        <div class="col-md-4">
            <label>Bulan</label>
            <select name="month" class="form-control">
                <option value="">Semua Bulan</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0,0,0,$m,1)) }}
                    </option>
                @endfor
            </select>
        </div>

        <div class="col-md-4">
            <label>Tahun</label>
            <select name="year" class="form-control">
                <option value="">Semua Tahun</option>
                @for($y = date('Y'); $y >= 2020; $y--)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>
        </div>

        <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-primary w-100">
                Filter
            </button>
        </div>

    </form>

    {{-- ===================== --}}
    {{-- TABEL --}}
    {{-- ===================== --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h5 class="mb-0">Data Laporan Penjualan</h5>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Jenis</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>

                    {{-- ORDERS --}}
                    @foreach($orders as $order)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><span class="badge bg-primary">Order</span></td>
                        <td>#ORD{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $order->user->name ?? '-' }}</td>
                        <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td><span class="badge bg-success">{{ ucfirst($order->status) }}</span></td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                    </tr>
                    @endforeach

                    {{-- PREORDERS --}}
                    @foreach($preorders as $preorder)
                    <tr>
                        <td>*</td>
                        <td><span class="badge bg-warning">Preorder</span></td>
                        <td>#PO{{ str_pad($preorder->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $preorder->user->name ?? '-' }}</td>
                        <td>Rp {{ number_format($preorder->total_amount, 0, ',', '.') }}</td>
                        <td><span class="badge bg-success">{{ ucfirst($preorder->status) }}</span></td>
                        <td>{{ $preorder->created_at->format('d M Y') }}</td>
                    </tr>
                    @endforeach

                    @if($orders->count() == 0 && $preorders->count() == 0)
                    <tr>
                        <td colspan="7" class="text-center">
                            Tidak ada data laporan
                        </td>
                    </tr>
                    @endif

                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
