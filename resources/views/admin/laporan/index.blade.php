@extends('layouts.template')

@section('title', 'Laporan Penjualan')

@section('content')
<style>
    :root {
        --sanjai-orange: #ff7810;
        --sanjai-orange-soft: #fff1e6;
        --sanjai-text: #4a2c0a;
    }

    /* Styling Tampilan Web */
    .stat-card { border: none; border-radius: 15px; transition: transform 0.2s; }
    .icon-box { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
    .filter-card { background-color: #fff; border-radius: 15px; border: 1px solid #eee; }
    .btn-orange { background-color: var(--sanjai-orange); color: white; font-weight: 600; border-radius: 10px; }
    .btn-download { background-color: #27ae60; color: white; font-weight: 600; border-radius: 10px; }
    .table thead th { background-color: #f8f9fa; text-transform: uppercase; font-size: 0.75rem; padding: 15px; border-bottom: 2px solid #eee; }

    /* Elemen yang hanya muncul saat di-print */
    .print-only { display: none; }

    /* ==========================================
       CSS KHUSUS PRINT (CETAK)
    ========================================== */
    @media print {
        nav, .navbar, .sidebar, .no-print, .btn, .filter-card, .stat-card, footer, .card-header span {
            display: none !important;
        }

        .print-only {
            display: block !important;
        }

        .print-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .container { width: 100% !important; max-width: 100% !important; padding: 0 !important; margin: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }

        .table { width: 100% !important; border-collapse: collapse !important; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #000 !important; padding: 8px !important; font-size: 10pt !important; color: #000 !important; }

        .print-footer {
            margin-top: 20px;
            float: right;
            width: 300px;
        }

        .summary-box {
            border: 1px solid #000;
            padding: 10px;
            margin-top: 10px;
        }
    }
</style>

<div class="container py-4">

    {{-- Header Khusus Saat Dicetak --}}
    <div class="print-only print-header">
        <h2 class="fw-bold mb-0">LAPORAN PENJUALAN KERIPIK SANJAI</h2>
        <p class="mb-0">Periode: {{ request('month') ? date('F', mktime(0,0,0,request('month'),1)) : 'Semua Bulan' }} {{ request('year') ?? 'Semua Tahun' }}</p>
        <small>Dicetak pada: {{ date('d/m/Y H:i') }}</small>
    </div>

    {{-- Header Web --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h3 class="fw-bold mb-0" style="color: var(--sanjai-text);">Laporan Penjualan</h3>
            <p class="text-muted mb-0">Pantau performa bisnis Sanjai Anda</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-light border shadow-sm px-3">
                <i class="fas fa-print me-2"></i> Print Tabel
            </button>
            <a href="{{ route('admin.laporan.export', request()->all()) }}" class="btn btn-download shadow-sm px-3">
                <i class="fas fa-file-excel me-2"></i> Unduh Excel
            </a>
        </div>
    </div>

    {{-- Statistik Web --}}
    <div class="row mb-4 g-3 no-print">
        <div class="col-md-4">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-box bg-success bg-opacity-10 text-success me-3"><i class="fas fa-wallet"></i></div>
                    <div>
                        <small class="text-muted d-block">Total Pendapatan</small>
                        <h4 class="fw-bold mb-0">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary me-3"><i class="fas fa-shopping-bag"></i></div>
                    <div>
                        <small class="text-muted d-block">Total Pesanan</small>
                        <h4 class="fw-bold mb-0">{{ $totalOrders }}</h4>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="col-md-4">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning me-3"><i class="fas fa-boxes"></i></div>
                    <div>
                        <small class="text-muted d-block">Produk Terjual</small>
                        <h4 class="fw-bold mb-0">{{ $totalSold }} Pcs</h4>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>

    {{-- Filter --}}
    <div class="card filter-card shadow-sm mb-4 no-print">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Pilih Bulan</label>
                    <select name="month" class="form-select">
                        <option value="">Semua Bulan</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Pilih Tahun</label>
                    <select name="year" class="form-select">
                        <option value="">Semua Tahun</option>
                        @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-orange w-100"><i class="fas fa-filter me-2"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Laporan --}}
    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="mb-0 fw-bold" style="color: var(--sanjai-text);">Rincian Transaksi</h5>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-center">Tipe</th>
                        <th>Kode Transaksi</th>
                        <th>Nama Pelanggan</th>
                        <th>Tanggal</th>
                        <th>Total Bayar</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td class="text-center">Order</td>
                        <td class="fw-bold">#ORD{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $order->user->name ?? 'Guest' }}</td>
                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                        <td class="fw-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td>Selesai</td>
                    </tr>
                    @empty @endforelse

                    @forelse($preorders as $preorder)
                    <tr>
                        <td class="text-center">Preorder</td>
                        <td class="fw-bold">#PO{{ str_pad($preorder->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $preorder->user->name ?? 'Guest' }}</td>
                        <td>{{ $preorder->created_at->format('d/m/Y') }}</td>
                        <td class="fw-bold">Rp {{ number_format($preorder->total_amount, 0, ',', '.') }}</td>
                        <td>Selesai</td>
                    </tr>
                    @empty @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Ringkasan Total Khusus Print --}}
    <div class="print-only print-footer">
        <div class="summary-box">
            <table class="table table-borderless mb-0">
                <tr>
                    <td>Total Pesanan</td>
                    <td class="text-end">: {{ $totalOrders }} Pesanan</td>
                </tr>
                <tr>
                    <td>Produk Terjual</td>
                    <td class="text-end">: {{ $totalSold }} Pcs</td>
                </tr>
                <tr class="fw-bold" style="border-top: 1px solid #000;">
                    <td>TOTAL PENDAPATAN</td>
                    <td class="text-end">: Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
        <div class="mt-4 text-center">
            <p>Mengetahui,</p>
            <br><br><br>
            <p class="fw-bold">( Admin Sanjai )</p>
        </div>
    </div>

</div>
@endsection
