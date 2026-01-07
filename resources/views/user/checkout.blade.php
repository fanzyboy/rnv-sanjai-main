@extends('layouts.main')
@section('title', 'Checkout - R&V Sanjai')

@section('content')
<div class="checkout-hero">
    <div class="container text-center">
        <div class="hero-badge" data-aos="fade-down"><i class="fas fa-shopping-basket me-2"></i>Selesaikan Pesanan</div>
        <h1 class="hero-title mt-3">Checkout</h1>
        <p class="hero-subtitle">Hampir selesai! Pastikan data pengiriman Anda sudah benar.</p>
    </div>
</div>

<div class="container py-5">
    @if (count($keranjang) > 0)
    <div class="row g-4">

        {{-- 1. Ringkasan Pesanan (Muncul PERTAMA di Mobile, KEDUA di Desktop) --}}
        <div class="col-lg-5 order-first order-lg-last">
            <div class="card shadow-sm border-0 sticky-top summary-card" style="top: 100px;">
                <div class="card-header bg-orange text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-receipt me-2"></i>Ringkasan Pesanan</h5>
                </div>
                <div class="card-body p-4">
                    @foreach ($keranjang as $item)
                    <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                        <div>
                            <h6 class="mb-1 fw-bold text-dark">{{ $item['produk'] }}</h6>
                            <span class="badge bg-light text-muted fw-normal p-2">{{ $item['gram'] }} x {{ $item['qty'] }}</span>
                        </div>
                        <strong class="text-orange">
                            Rp {{ number_format($item['total'], 0, ',', '.') }}
                        </strong>
                    </div>
                    @endforeach

                    <div class="total-section d-flex justify-content-between align-items-center pt-3 mt-2">
                        <h5 class="mb-0 fw-bold">Total Bayar</h5>
                        <h3 class="mb-0 text-orange fw-800">
                            Rp {{ number_format(array_sum(array_column($keranjang, 'total')), 0, ',', '.') }}
                        </h3>
                    </div>

                    <div class="alert alert-warning-light mt-4 border-0 rounded-3">
                        <small class="text-orange-dark"><i class="fas fa-info-circle me-1"></i> Harga belum termasuk ongkir (akan dihitung admin via WhatsApp).</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Form Data Pembeli (Muncul KEDUA di Mobile, PERTAMA di Desktop) --}}
        <div class="col-lg-7 order-last order-lg-first">
            <div class="card shadow-sm border-0 checkout-form-card">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-brown"><i class="fas fa-shipping-fast me-2"></i>Informasi Pengiriman & Pembayaran</h5>
                </div>
                <div class="card-body p-4">

                    <form action="{{ route('checkout.simpan') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @foreach($keranjang as $item)
                            <input type="hidden" name="cart_ids[]" value="{{ $item['id'] }}">
                        @endforeach

                        <input type="hidden" name="total_amount" value="{{ array_sum(array_column($keranjang, 'total')) }}">

                        {{-- Nama Lengkap --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-user text-orange"></i></span>
                                <input type="text" name="nama" class="form-control border-0 bg-light shadow-none" value="{{ Auth::user()->name }}" readonly required>
                            </div>
                        </div>

                        {{-- Alamat --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Alamat Lengkap Pengiriman</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-map-marker-alt text-orange"></i></span>
                                <textarea name="alamat" class="form-control border-0 bg-light shadow-none" rows="3" placeholder="Masukkan alamat lengkap pengiriman" required>{{ Auth::user()->alamat }}</textarea>
                            </div>
                        </div>

                        {{-- WhatsApp --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">No. Telepon / WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fab fa-whatsapp text-orange"></i></span>
                                <input type="text" name="telepon" id="no_hp" class="form-control border-0 bg-light shadow-none" value="{{ Auth::user()->no_hp }}" placeholder="Contoh: 08123456789" required>
                            </div>
                        </div>

                        {{-- Nama Bank & No Rekening (Info Refund) --}}
                        <div class="card bg-light border-0 mb-4 rounded-3">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <small class="text-orange-dark fw-bold d-block mb-2">
                                            <i class="fas fa-info-circle me-1"></i> Informasi Rekening Anda (Opsional)
                                        </small>
                                        <p class="text-muted mb-3" style="font-size: 0.8rem; line-height: 1.4;">
                                            Data ini kami butuhkan untuk mempermudah proses <strong>pengembalian dana (refund)</strong> secara cepat jika stok habis atau terjadi pembatalan pesanan.
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold text-secondary">Nama Bank</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-0"><i class="fas fa-university text-orange"></i></span>
                                            <input type="text" name="nama_bank" class="form-control border-0 shadow-none" value="{{ Auth::user()->nama_bank }}" placeholder="Contoh: BCA / BRI">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-bold text-secondary">Nomor Rekening</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-0"><i class="fas fa-credit-card text-orange"></i></span>
                                            <input type="text" name="nomor_rekening" class="form-control border-0 shadow-none" value="{{ Auth::user()->nomor_rekening }}" placeholder="Masukkan nomor rekening">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Metode Pembayaran --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary mb-3">Pilih Metode Pembayaran</label>
                            <div class="row g-3">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="metode" id="metode_cod" value="cod" required onchange="toggleBukti(this)">
                                    <label class="payment-option btn btn-outline-orange w-100 py-3" for="metode_cod">
                                        <i class="fas fa-truck-loading fa-2x mb-2 d-block"></i>
                                        <strong>COD</strong>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="metode" id="metode_transfer" value="transfer" required onchange="toggleBukti(this)">
                                    <label class="payment-option btn btn-outline-orange w-100 py-3" for="metode_transfer">
                                        <i class="fas fa-university fa-2x mb-2 d-block"></i>
                                        <strong>Transfer</strong>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Section Bukti Pembayaran --}}
                        <div id="bukti_section" class="mb-4 d-none">
                            <div class="card border-orange-dashed bg-orange-light shadow-none">
                                <div class="card-body">
                                    <label class="form-label fw-bold text-orange-dark"><i class="fas fa-camera me-2"></i>Unggah Bukti Transfer</label>
                                    <div class="bank-info mb-3 p-3 bg-white rounded-3 border">
                                        <p class="small mb-1 text-muted">Transfer ke Rekening Resmi:</p>
                                        <h6 class="mb-0 fw-bold text-dark">BNI 17056789 <br> <span class="text-orange">A/N R&V SANJAI</span></h6>
                                    </div>
                                    <input type="file" name="bukti" id="bukti_input" class="form-control border-0" accept="image/*">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary-orange btn-lg w-100 py-3 mt-3 shadow">
                            <i class="fas fa-shopping-bag me-2"></i>Konfirmasi Pesanan
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    @else
    <div class="text-center py-5">
        <div class="empty-state-icon mb-4">
            <i class="fas fa-shopping-basket fa-4x text-orange opacity-25"></i>
        </div>
        <h4 class="fw-bold">Keranjang Anda Kosong</h4>
        <a href="{{ route('produk') }}" class="btn btn-primary-orange px-5 py-3 mt-3">
            <i class="fas fa-arrow-left me-2"></i>Kembali Belanja
        </a>
    </div>
    @endif
</div>

<style>
    :root {
        --orange-primary: #dc6900;
        --orange-hover: #b85600;
        --brown-dark: #4a2508;
    }

    .fw-800 { font-weight: 800; }
    .text-orange { color: var(--orange-primary); }
    .text-orange-dark { color: var(--orange-hover); }
    .text-brown { color: var(--brown-dark); }
    .bg-orange { background-color: var(--orange-primary); }
    .bg-orange-light { background-color: #fff8f2; }
    .border-orange-dashed { border: 2px dashed #ffcca0; }

    /* Hero Section */
    .checkout-hero {
        background: linear-gradient(135deg, #dc6900 0%, #ff9e43 100%);
        color: white;
        padding: 60px 0;
        border-radius: 0 0 40px 40px;
    }
    .hero-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 8px 24px;
        border-radius: 50px;
        display: inline-block;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .hero-title { font-size: 2.5rem; font-weight: 800; }

    /* Forms & Cards */
    .card { border-radius: 20px; overflow: hidden; border: none; }
    .checkout-form-card { border: 1px solid #f0f0f0; }
    .form-control { border-radius: 12px; padding: 12px 15px; }
    .input-group-text { border-radius: 12px 0 0 12px; }

    /* Payment Options */
    .payment-option {
        border: 2px solid #f0f0f0;
        border-radius: 16px;
        color: #6c757d;
        transition: all 0.3s ease;
    }
    .btn-check:checked + .payment-option {
        background-color: #fff8f2;
        border-color: var(--orange-primary);
        color: var(--orange-primary);
        transform: translateY(-2px);
    }

    /* Buttons */
    .btn-primary-orange {
        background-color: var(--orange-primary);
        color: white;
        border: none;
        border-radius: 15px;
        font-weight: 700;
        transition: 0.3s;
    }
    .btn-primary-orange:hover {
        background-color: var(--orange-hover);
        color: white;
        box-shadow: 0 10px 20px rgba(220, 105, 0, 0.2);
    }

    .alert-warning-light { background-color: #fff3e6; color: #856404; }

    @media (max-width: 991px) {
        .summary-card { position: relative !important; top: 0 !important; margin-bottom: 20px; }
        .hero-title { font-size: 2rem; }
    }
</style>

<script>
function toggleBukti(radio) {
    const buktiSection = document.getElementById('bukti_section');
    const buktiInput = document.getElementById('bukti_input');

    if (radio.value === 'transfer') {
        buktiSection.classList.remove('d-none');
        buktiInput.setAttribute('required', 'required');
    } else {
        buktiSection.classList.add('d-none');
        buktiInput.removeAttribute('required');
        buktiInput.value = '';
    }
}

document.querySelector('form')?.addEventListener('submit', function() {
    const b = this.querySelector('button[type="submit"]');
    b.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
    b.disabled = true;
});
</script>
@endsection
