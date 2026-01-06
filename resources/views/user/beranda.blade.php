@extends('layouts.main')

@section('title', 'Beranda - R&V Sanjai')

@section('content')
    {{-- Hero Section --}}
    <div class="hero-section overflow-hidden">
        <div class="container">
            <div class="row align-items-center min-vh-100 py-5">
                <div class="col-lg-6 hero-content text-center text-lg-start" data-aos="fade-right">
                    <div class="hero-badge mb-4">
                        <span class="badge-custom">
                            <i class="fas fa-certificate text-warning me-2"></i>Kualitas Premium Minangkabau
                        </span>
                    </div>
                    <h1 class="hero-title">
                        Nikmati Kelezatan <br>
                        <span class="text-orange">Autentik R&V Sanjai</span>
                    </h1>
                    <p class="hero-subtitle mt-3">
                        Keripik Sanjai pilihan dari Lubuk Minturun, Kota Padang. Diolah secara higienis dengan bumbu rempah warisan untuk kelezatan yang tak terlupakan.
                    </p>
                    <div class="hero-buttons d-flex justify-content-center justify-content-lg-start gap-3 mt-5">
                        <a href="{{ route('produk') }}" class="btn btn-primary-custom">
                            <i class="fas fa-shopping-cart me-2"></i>Pesan Sekarang
                        </a>
                        <a href="{{ route('tentang') }}" class="btn btn-outline-custom">
                            Tentang Kami
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0 text-center position-relative" data-aos="zoom-in">
                    <div class="hero-image-wrapper">
                        <div class="blob-bg"></div>
                        <img src="{{ asset('images/keripik.jpg') }}" class="img-fluid hero-product-img shadow-lg" alt="Keripik R&V Sanjai">
                        <div class="experience-card shadow">
                            <span class="num">4+</span>
                            <span class="txt">Tahun <br>Menjaga Rasa</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-shape-1"></div>
        <div class="hero-shape-2"></div>
    </div>

    {{-- Top Rated Products Section --}}
    <section class="py-5 position-relative bg-white">
        <div class="container">
            <div class="section-header text-center mb-5" data-aos="fade-up">
                <span class="text-orange fw-bold text-uppercase tracking-widest small">Koleksi Terbaik</span>
                <h2 class="section-title mt-2">⭐ Produk Rating Tertinggi</h2>
                <div class="divider mx-auto"></div>
            </div>

            <div class="row g-4 justify-content-center">
                @foreach($bestSeller as $item)
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                        <div class="product-card-modern">
                            <div class="product-img-container">
                                @if($item->foto)
                                    <img src="{{ asset('storage/'.$item->foto) }}" alt="{{ $item->nama_produk }}">
                                @else
                                    <img src="{{ asset('images/default.png') }}" alt="{{ $item->nama_produk }}">
                                @endif
                                <div class="rating-badge-glass">
                                    <i class="fas fa-star me-1"></i>
                                    {{ $item->rata_rata ? number_format($item->rata_rata, 1) : '5.0' }}
                                    <span class="reviews-count">({{ $item->ratings->count() }})</span>
                                </div>
                            </div>
                            <div class="product-info p-4">
                                <h5 class="product-name">{{ $item->nama_produk }}</h5>
                                <p class="product-desc">{{ Str::limit($item->deskripsi, 80, '...') }}</p>

                                <div class="price-action-wrapper mt-4">
                                    <div class="price-group">
                                        @if($item->prices->count() > 0)
                                            <span class="currency">Rp</span>
                                            <span class="amount">{{ number_format($item->prices->first()->harga, 0, ',', '.') }}</span>
                                            <span class="unit">/{{ $item->prices->first()->berat }}g</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('produk') }}" class="btn-action">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- About Preview Section --}}
    <section class="about-preview py-5">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="image-stack position-relative">
                        <img src="{{ asset('asset/anim.png') }}" class="img-fluid rounded-custom shadow-lg main-img" alt="Produksi Sanjai">
                        <div class="floating-info-box shadow">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-circle bg-orange">
                                    <i class="fas fa-heart text-white"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fw-bold">100% Homemade</p>
                                    <small class="text-muted">Resep Tradisional</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="ps-lg-4">
                        <h6 class="text-orange fw-bold text-uppercase mb-2">Warisan Budaya</h6>
                        <h3 class="about-headline mb-4">Cita Rasa Minang dalam Setiap Gigitan</h3>
                        <p class="about-description text-secondary mb-4">
                            Didirikan di Lubuk Minturun, Padang, R&V Sanjai berkomitmen untuk menyajikan keripik sanjai dengan kualitas premium. Kami menjaga setiap proses mulai dari pemilihan singkong hingga pengemasan agar tetap renyah dan nikmat.
                        </p>
                        <div class="feature-list mb-5">
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Tanpa Bahan Pengawet Buatan</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Bumbu Rempah Alami Pilihan</span>
                            </div>
                        </div>
                        <a href="{{ route('tentang') }}" class="btn btn-primary-custom px-5">Selengkapnya</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        :root {
            --color-primary: #dc6900;
            --color-primary-dark: #b85600;
            --color-brown-dark: #4a2508;
            --color-bg-light: #fffaf5;
            --radius-custom: 24px;
        }

        .text-orange { color: var(--color-primary); }

        /* --- Hero Section --- */
        .hero-section {
            background-color: var(--color-bg-light);
            position: relative;
            background-image: radial-gradient(#dc6900 0.5px, transparent 0.5px);
            background-size: 24px 24px;
            background-attachment: fixed;
            background-color: #ffffff;
        }
        .hero-shape-1 {
            position: absolute; top: -100px; right: -100px; width: 400px; height: 400px;
            background: rgba(220, 105, 0, 0.05); border-radius: 50%; z-index: 0;
        }
        .hero-title {
            font-size: 3.8rem; font-weight: 800; color: var(--color-brown-dark); line-height: 1.1;
        }
        .hero-subtitle { font-size: 1.2rem; color: #5a5a5a; max-width: 520px; line-height: 1.6; }
        .badge-custom {
            background: #fff; padding: 10px 20px; border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); font-weight: 600; font-size: 0.9rem;
        }
        .hero-image-wrapper { position: relative; z-index: 2; }
        .hero-product-img { border-radius: var(--radius-custom); max-width: 450px; }
        .blob-bg {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            width: 110%; height: 110%; background: #fff1e6; border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
            z-index: -1; animation: blobAnimate 10s infinite linear;
        }
        @keyframes blobAnimate { 0% { border-radius: 40% 60% 70% 30% / 40% 40% 60% 50%; } 50% { border-radius: 60% 40% 30% 70% / 50% 60% 40% 60%; } 100% { border-radius: 40% 60% 70% 30% / 40% 40% 60% 50%; } }

        /* --- Product Cards --- */
        .product-card-modern {
            background: #fff; border-radius: var(--radius-custom);
            transition: all 0.4s ease; border: 1px solid #f0f0f0; height: 100%;
        }
        .product-card-modern:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }
        .product-img-container {
            height: 250px; overflow: hidden; position: relative; border-radius: var(--radius-custom) var(--radius-custom) 0 0;
        }
        .product-img-container img { width: 100%; height: 100%; object-fit: cover; }
        .rating-badge-glass {
            position: absolute; top: 15px; right: 15px; background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px); padding: 6px 14px; border-radius: 50px;
            font-weight: 700; font-size: 0.9rem; color: #333;
        }
        .product-name { color: var(--color-brown-dark); font-weight: 700; }
        .price-group .amount { font-size: 1.6rem; font-weight: 800; color: var(--color-primary); }
        .price-group .unit { font-size: 0.85rem; color: #888; margin-left: 2px; }
        .btn-action {
            width: 45px; height: 45px; background: var(--color-primary); color: #fff;
            border-radius: 12px; display: flex; align-items: center; justify-content: center;
            text-decoration: none; transition: 0.3s;
        }
        .btn-action:hover { background: var(--color-primary-dark); color: #fff; }
        .price-action-wrapper { display: flex; justify-content: space-between; align-items: center; }

        /* --- Buttons --- */
        .btn-primary-custom {
            background: var(--color-primary); color: #fff; padding: 14px 32px;
            border-radius: 16px; font-weight: 700; border: none; transition: 0.3s;
        }
        .btn-primary-custom:hover { background: var(--color-primary-dark); transform: translateY(-3px); color: #fff; }
        .btn-outline-custom {
            border: 2px solid var(--color-primary); color: var(--color-primary);
            padding: 14px 32px; border-radius: 16px; font-weight: 700; transition: 0.3s;
        }
        .btn-outline-custom:hover { background: var(--color-primary); color: #fff; }

        /* --- About Section --- */
        .rounded-custom { border-radius: 30px; }
        .about-headline { font-size: 2.5rem; font-weight: 800; color: var(--color-brown-dark); }
        .feature-item { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
        .feature-item i { color: #28a745; font-size: 1.2rem; }
        .feature-item span { font-weight: 600; color: #444; }
        .floating-info-box {
            position: absolute; bottom: 30px; left: -20px; background: #fff;
            padding: 20px; border-radius: 20px; min-width: 220px;
        }
        .bg-orange { background: var(--color-primary); }
        .icon-circle { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }

        /* --- Global Helpers --- */
        .divider { width: 60px; height: 4px; background: var(--color-primary); border-radius: 2px; margin-top: 15px; }
        .experience-card {
            position: absolute; bottom: 30px; left: -10px; background: #fff;
            padding: 15px 25px; border-radius: 18px; text-align: center; border-bottom: 4px solid var(--color-primary);
        }
        .experience-card .num { font-size: 2rem; font-weight: 800; color: var(--color-primary); line-height: 1; }
        .experience-card .txt { font-size: 0.75rem; font-weight: 700; display: block; margin-top: 5px; }

        @media (max-width: 768px) {
            .hero-title { font-size: 2.6rem; }
            .hero-buttons { flex-direction: column; }
            .hero-product-img { max-width: 100%; }
            .floating-info-box { position: static; margin-top: 20px; }
        }
    </style>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 900, once: true });
    </script>
@endsection
