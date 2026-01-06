@extends('layouts.template')

@section('title', 'Tambah Produk')

@section('content')
<style>
    :root {
        --sanjai-orange: #ff7810;
        --sanjai-orange-soft: #fff1e6;
        --sanjai-orange-dark: #e66a0d;
        --sanjai-text: #4a2c0a;
    }

    .card {
        border: none;
        border-radius: 16px;
        background-color: #ffffff;
    }

    .card-header {
        border-radius: 16px 16px 0 0 !important;
        background-color: #ffffff;
        border-bottom: 1px solid #f0f0f0;
        padding: 1.5rem;
    }

    .header-icon {
        width: 40px;
        height: 40px;
        background-color: var(--sanjai-orange-soft);
        color: var(--sanjai-orange);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        margin-right: 12px;
    }

    .form-label {
        font-weight: 600;
        color: var(--sanjai-text);
        font-size: 0.85rem;
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border-radius: 10px;
        border: 1px solid #e0e0e0;
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--sanjai-orange);
        box-shadow: 0 0 0 0.25rem rgba(255, 120, 16, 0.1);
    }

    .section-title {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--sanjai-orange);
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .section-title::after {
        content: "";
        flex: 1;
        height: 1px;
        background: #eee;
        margin-left: 10px;
    }

    .variasi-item {
        background-color: #ffffff;
        padding: 1.25rem;
        border-radius: 12px;
        border: 1px solid #eee;
        border-left: 4px solid var(--sanjai-orange);
        margin-bottom: 1rem;
        transition: all 0.2s;
    }

    .variasi-item:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border-color: var(--sanjai-orange-soft);
    }

    .btn-orange {
        background-color: var(--sanjai-orange);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-orange:hover {
        background-color: var(--sanjai-orange-dark);
        color: white;
        transform: translateY(-1px);
    }

    .btn-outline-orange {
        color: var(--sanjai-orange);
        border: 1px solid var(--sanjai-orange);
        background-color: transparent;
        border-radius: 10px;
        font-weight: 600;
    }

    .btn-outline-orange:hover {
        background-color: var(--sanjai-orange-soft);
        color: var(--sanjai-orange-dark);
        border-color: var(--sanjai-orange-dark);
    }

    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
        color: var(--sanjai-text);
        font-weight: 600;
    }

    /* Responsive Mobile */
    @media (max-width: 768px) {
        .border-start { border: none !important; border-top: 1px solid #eee !important; margin-top: 2rem; padding-top: 2rem; padding-left: 0 !important; }
        .d-flex.gap-2 { flex-direction: column-reverse; }
        .btn-orange, .btn-light { width: 100%; }
        .variasi-item { padding: 1rem; }
    }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-11 col-xl-10">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center">
                    <div class="header-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold" style="color: var(--sanjai-text);">Tambah Produk</h5>
                        <small class="text-muted">Lengkapi detail produk Sanjai Anda</small>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            {{-- Form Kiri --}}
                            <div class="col-md-7 pe-md-4">
                                <h6 class="section-title">Informasi Dasar</h6>

                                <div class="mb-3">
                                    <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_produk" class="form-control" placeholder="Contoh: Sanjai Balado Spesial" required>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6 mb-3">
                                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                        <select name="jenis_produk" class="form-select" required>
                                            <option value="" disabled selected>Pilih...</option>
                                            <option value="manis">Manis</option>
                                            <option value="pedas">Pedas</option>
                                            <option value="gurih">Gurih</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <label class="form-label">Foto Produk</label>
                                        <input type="file" name="foto" class="form-control">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control" rows="5" placeholder="Ceritakan keunikan rasa produk ini..."></textarea>
                                </div>
                            </div>

                            {{-- Form Kanan (Variasi) --}}
                            <div class="col-md-5 border-start ps-md-4">
                                <h6 class="section-title">Varian Berat & Stok</h6>

                                <div id="variasi-container">
                                    <div class="variasi-item shadow-sm">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="form-label mb-1">Berat (gr)</label>
                                                <input type="number" name="variasi[0][berat]" class="form-control" placeholder="250" required>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label mb-1">Stok</label>
                                                <input type="number" name="variasi[0][stok]" class="form-control" placeholder="10" required>
                                            </div>
                                            <div class="col-12 mt-2">
                                                <label class="form-label mb-1">Harga</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="number" name="variasi[0][harga]" class="form-control" placeholder="25000" required>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-3">
                                                <button type="button" class="btn btn-link text-danger btn-sm p-0 remove-variasi-btn" style="display:none; text-decoration:none;">
                                                    <i class="fas fa-times-circle me-1"></i> Hapus Varian
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-outline-orange btn-sm w-100 mt-2" id="tambahVariasiBtn">
                                    <i class="fas fa-plus me-1"></i> Tambah Varian Berat
                                </button>
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-top d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-light border px-4">Batal</a>
                            <button type="submit" class="btn btn-orange px-5 shadow-sm">
                                <i class="fas fa-save me-2"></i> Simpan ke Katalog
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let index = 1;
        const container = document.getElementById('variasi-container');
        const tambahBtn = document.getElementById('tambahVariasiBtn');

        function updateRemoveButtons() {
            const variasiItems = container.querySelectorAll('.variasi-item');
            variasiItems.forEach((item) => {
                const btn = item.querySelector('.remove-variasi-btn');
                btn.style.display = (variasiItems.length > 1) ? 'block' : 'none';
            });
        }

        tambahBtn.addEventListener('click', function() {
            const html = `
                <div class="variasi-item shadow-sm">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label mb-1">Berat (gr)</label>
                            <input type="number" name="variasi[${index}][berat]" class="form-control" placeholder="250" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label mb-1">Stok</label>
                            <input type="number" name="variasi[${index}][stok]" class="form-control" placeholder="10" required>
                        </div>
                        <div class="col-12 mt-2">
                            <label class="form-label mb-1">Harga</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="variasi[${index}][harga]" class="form-control" placeholder="25000" required>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="button" class="btn btn-link text-danger btn-sm p-0 remove-variasi-btn" style="text-decoration:none;">
                                <i class="fas fa-times-circle me-1"></i> Hapus Varian
                            </button>
                        </div>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
            index++;
            updateRemoveButtons();
        });

        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-variasi-btn')) {
                e.target.closest('.variasi-item').remove();
                updateRemoveButtons();
            }
        });

        updateRemoveButtons();
    });
</script>
@endsection
