@extends('layouts.template')

@section('title', 'Kelola Produk')

@section('content')
<style>
    :root {
        --sanjai-orange: #ff7810;
        --sanjai-orange-soft: #fff1e6;
        --sanjai-orange-dark: #e66a0d;
        --sanjai-text: #4a2c0a;
    }

    /* General Styling */
    .btn-orange {
        background-color: var(--sanjai-orange);
        color: white;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-orange:hover { background-color: var(--sanjai-orange-dark); color: white; transform: translateY(-2px); }

    .table-container { background: white; border-radius: 15px; overflow: hidden; border: 1px solid #eee; }

    /* Status Stok Badge */
    .stok-aman { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
    .stok-tipis { background-color: #fff3cd; color: #664d03; border: 1px solid #ffecb5; }
    .stok-habis { background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; animation: pulse-danger 2s infinite; }

    /* Animasi Penanda Stok Habis */
    @keyframes pulse-danger {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
        70% { transform: scale(1.02); box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }

    .badge-stok {
        font-size: 0.7rem;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 700;
        text-transform: uppercase;
    }

    /* Mobile UI */
    .mobile-product-card { border: none; border-radius: 16px; border: 1px solid #f0f0f0; margin-bottom: 1rem; }
    .variant-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 12px;
        background: #fdfdfd;
        border: 1px solid #f1f1f1;
        border-radius: 10px;
        margin-bottom: 6px;
    }

    @media (max-width: 768px) {
        .page-title { font-size: 1.25rem; }
    }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-title fw-bold mb-0" style="color: var(--sanjai-text);">Daftar Produk</h3>
            <p class="text-muted mb-0 d-none d-sm-block">Pantau ketersediaan stok Sanjai Anda</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-orange shadow-sm">
            <i class="fas fa-plus me-2"></i>Tambah Produk
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center">
            <i class="fas fa-check-circle me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    {{-- Desktop View --}}
    <div class="table-container shadow-sm d-none d-md-block">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Produk</th>
                        <th>Kategori</th>
                        <th width="350">Varian & Status Stok</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <img src="{{ $p->foto ? asset('storage/'.$p->foto) : asset('images/default.png') }}"
                                     class="rounded-3 me-3 border" width="55" height="55" style="object-fit: cover;">
                                <div>
                                    <div class="fw-bold" style="color: var(--sanjai-text);">{{ $p->nama_produk }}</div>
                                    <small class="text-muted">ID: #PRD-{{ $p->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border-0" style="font-weight: 600;">
                                <i class="fas fa-tag me-1 text-orange"></i> {{ ucfirst($p->jenis_produk) }}
                            </span>
                        </td>
                        <td>
                            @foreach($p->prices as $price)
                            <div class="d-flex align-items-center mb-2">
                                <div style="width: 80px;" class="small fw-bold">{{ $price->berat }} gr</div>
                                <div class="flex-grow-1">
                                    @php
                                        $statusClass = $price->stok > 10 ? 'stok-aman' : ($price->stok > 0 ? 'stok-tipis' : 'stok-habis');
                                        $statusText = $price->stok > 10 ? 'Stok Tersedia' : ($price->stok > 0 ? 'Hampir Habis' : 'Habis');
                                    @endphp
                                    <span class="badge-stok {{ $statusClass }}">
                                        @if($price->stok == 0) <i class="fas fa-exclamation-triangle me-1"></i> @endif
                                        {{ $statusText }}: {{ $price->stok }} Pcs
                                    </span>
                                </div>
                                <div class="text-primary fw-bold small">Rp {{ number_format($price->harga, 0, ',', '.') }}</div>
                            </div>
                            @endforeach
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group border rounded-3">
                                <a href="{{ route('admin.products.edit', $p->id) }}" class="btn btn-white btn-sm px-3" title="Edit">
                                    <i class="fas fa-edit text-warning"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus produk?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-white btn-sm px-3 border-start" title="Hapus">
                                        <i class="fas fa-trash-alt text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-5 text-muted">Belum ada produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile View --}}
    <div class="d-md-none">
        @forelse($products as $p)
            <div class="card mobile-product-card shadow-sm overflow-hidden">
                <div class="card-header bg-white py-3 border-0 d-flex align-items-center">
                    <img src="{{ $p->foto ? asset('storage/'.$p->foto) : asset('images/default.png') }}"
                         class="rounded-3 me-3 border" width="50" height="50" style="object-fit: cover;">
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-0" style="color: var(--sanjai-text);">{{ $p->nama_produk }}</h6>
                        <small class="text-orange fw-bold">{{ ucfirst($p->jenis_produk) }}</small>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a class="dropdown-item" href="{{ route('admin.products.edit', $p->id) }}"><i class="fas fa-edit me-2 text-warning"></i>Edit</a></li>
                            <li>
                                <form action="{{ route('admin.products.destroy', $p->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="dropdown-item text-danger" onclick="return confirm('Hapus?')"><i class="fas fa-trash-alt me-2"></i>Hapus</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body pt-0 px-3 pb-3">
                    <p class="text-muted small mb-3">Daftar Stok Varian:</p>
                    @foreach($p->prices as $price)
                        <div class="variant-row shadow-sm">
                            <div>
                                <span class="fw-bold d-block">{{ $price->berat }} gr</span>
                                <span class="text-primary small fw-bold">Rp {{ number_format($price->harga, 0, ',', '.') }}</span>
                            </div>
                            <div class="text-end">
                                @php
                                    $statusClass = $price->stok > 10 ? 'stok-aman' : ($price->stok > 0 ? 'stok-tipis' : 'stok-habis');
                                    $statusText = $price->stok > 10 ? 'Stok Tersedia' : ($price->stok > 0 ? 'Hampir Habis' : 'Habis');
                                @endphp
                                <span class="badge-stok {{ $statusClass }} d-inline-block">
                                    {{ $price->stok }} Pcs
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="alert alert-info text-center">Belum ada produk.</div>
        @endforelse
    </div>
</div>
@endsection
