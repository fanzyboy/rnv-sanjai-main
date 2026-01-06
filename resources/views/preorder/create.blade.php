@extends('layouts.main')

@section('title', 'Form Pre-Order')

@section('content')
<style>
    :root {
        --sanjai-orange: #ff7810;
        --sanjai-orange-hover: #e66a0d;
        --sanjai-soft-orange: #fff4ed;
    }

    .preorder-card {
        border: none;
        border-top: 5px solid var(--sanjai-orange);
    }

    .bg-sanjai-orange {
        background-color: var(--sanjai-orange) !important;
    }

    .btn-sanjai {
        background-color: var(--sanjai-orange);
        color: white;
        border: none;
        transition: 0.3s;
    }

    .btn-sanjai:hover {
        background-color: var(--sanjai-orange-hover);
        color: white;
        transform: translateY(-2px);
    }

    .form-label {
        font-weight: 600;
        color: #444;
    }

    .bank-info-box {
        background-color: var(--sanjai-soft-orange);
        border: 1px dashed var(--sanjai-orange);
        border-radius: 8px;
    }

    .admin-account-box {
        background: #f8f9fa;
        border-left: 4px solid #0056b3;
        border-radius: 12px;
        position: relative;
    }

    .btn-copy {
        font-size: 0.75rem;
        padding: 2px 10px;
        border-radius: 6px;
    }

    .total-price-badge {
        font-size: 1.25rem;
        color: var(--sanjai-orange);
        font-weight: 800;
    }
</style>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-lg rounded-3 preorder-card">
                <div class="card-header bg-sanjai-orange text-white py-3">
                    <h4 class="mb-0 text-center">
                        <i class="fas fa-shopping-basket me-2"></i> Konfirmasi Pre-Order
                    </h4>
                </div>

                <div class="card-body p-4">

                    {{-- Pesan Alert --}}
                    @if ($errors->any())
                        <div class="alert alert-danger mb-4 border-0 shadow-sm">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li><i class="fas fa-exclamation-triangle me-1"></i> {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @isset($price)
                        @php
                            $totalHarga = $price->harga * $qty;
                        @endphp

                        <form action="{{ route('preorder.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="price_id" value="{{ $price->id }}">
                            <input type="hidden" name="qty" value="{{ $qty }}">

                            {{-- Detail Produk --}}
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">NAMA PRODUK</label>
                                    <div class="fw-bold p-2 bg-light border-start border-3 border-warning">
                                        {{ $price->product->nama_produk }}
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small">VARIAN & JUMLAH</label>
                                    <div class="fw-bold p-2 bg-light border-start border-3 border-warning">
                                        {{ $price->berat }} gr | {{ $qty }} pcs
                                    </div>
                                </div>
                            </div>

                            {{-- Ringkasan Harga --}}
                            <div class="p-3 mb-4 rounded-3 border bg-light shadow-sm">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Harga Satuan:</span>
                                    <span>Rp {{ number_format($price->harga, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Jumlah Pesanan:</span>
                                    <span>x {{ $qty }}</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">Total Pembayaran:</span>
                                    <span class="total-price-badge">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <hr class="text-muted opacity-25">

                            {{-- Form Identitas Bank --}}
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Bank Anda</label>
                                    <input type="text" name="nama_bank"
                                           class="form-control @error('nama_bank') is-invalid @enderror"
                                           placeholder="BCA / Mandiri / BRI"
                                           value="{{ old('nama_bank', $user->nama_bank ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nomor Rekening Anda</label>
                                    <input type="text" name="nomor_rekening"
                                           class="form-control @error('nomor_rekening') is-invalid @enderror"
                                           placeholder="Nomor rekening Anda"
                                           value="{{ old('nomor_rekening', $user->nomor_rekening ?? '') }}">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Catatan Tambahan (Opsional)</label>
                                <textarea name="deskripsi" class="form-control" rows="2"
                                          placeholder="Contoh: Packing kayu atau lainnya...">{{ old('deskripsi') }}</textarea>
                            </div>

                            {{-- Pilihan Metode Pembayaran --}}
                            <div class="bank-info-box p-3 mb-4">
                                <label class="form-label d-block mb-3">Pilih Metode Pembayaran :</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check custom-radio">
                                        <input class="form-check-input" type="radio" name="metode_pembayaran"
                                               id="radioTransfer" value="transfer"
                                               {{ old('metode_pembayaran', 'transfer') === 'transfer' ? 'checked' : '' }}
                                               onchange="toggleBukti(true)">
                                        <label class="form-check-label fw-bold" for="radioTransfer">Transfer Bank</label>
                                    </div>
                                    <div class="form-check custom-radio">
                                        <input class="form-check-input" type="radio" name="metode_pembayaran"
                                               id="radioCod" value="cod"
                                               {{ old('metode_pembayaran') === 'cod' ? 'checked' : '' }}
                                               onchange="toggleBukti(false)">
                                        <label class="form-check-label fw-bold" for="radioCod">COD (Bayar di Tempat)</label>
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION TRANSFER --}}
                            <div id="transferDetailSection" style="display: none;">
                                <div class="admin-account-box p-3 mb-4 shadow-sm border">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="fw-bold text-primary mb-0"><i class="fas fa-university me-2"></i>Rekening Pembayaran</h6>
                                        <span class="badge bg-primary px-2">BCA</span>
                                    </div>
                                    <div class="bg-white p-2 rounded border d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <span class="d-block small text-muted">Nomor Rekening:</span>
                                            <h5 class="fw-bold mb-0 text-dark" id="norekAdmin">892012345678</h5>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-copy" onclick="copyNorek()">
                                            <i class="fas fa-copy me-1"></i> <span id="btnCopyText">Salin</span>
                                        </button>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="mb-0 small fw-semibold text-muted">a.n. R&V Sanjai Official</p>
                                        <small class="text-danger fw-bold">Nominal: Rp {{ number_format($totalHarga, 0, ',', '.') }}</small>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label text-danger">Upload Bukti Transfer <span class="badge bg-danger">Wajib</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fas fa-upload text-warning"></i></span>
                                        <input type="file" name="bukti_transfer" id="buktiTransferInput"
                                               class="form-control" accept="image/*">
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-between pt-3">
                                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary px-4 py-2">
                                    <i class="fas fa-times me-1"></i> Batalkan
                                </a>
                                <button type="submit" class="btn btn-sanjai px-5 py-2 fw-bold">
                                    Kirim Pre-Order <i class="fas fa-check-circle ms-1"></i>
                                </button>
                            </div>

                        </form>
                    @endisset

                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function toggleBukti(isTransfer) {
        const section = document.getElementById('transferDetailSection');
        const input   = document.getElementById('buktiTransferInput');

        if (isTransfer) {
            section.style.display = 'block';
            input.required = true;
            input.disabled = false;
        } else {
            section.style.display = 'none';
            input.required = false;
            input.disabled = true;
            input.value = '';
        }
    }

    function copyNorek() {
        const norek = document.getElementById('norekAdmin').innerText;
        const btnText = document.getElementById('btnCopyText');

        navigator.clipboard.writeText(norek).then(() => {
            btnText.innerText = 'Tersalin!';
            setTimeout(() => {
                btnText.innerText = 'Salin';
            }, 2000);
        }).catch(err => {
            console.error('Gagal menyalin: ', err);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const selected = document.querySelector('input[name="metode_pembayaran"]:checked');
        toggleBukti(selected && selected.value === 'transfer');
    });
</script>
@endsection
