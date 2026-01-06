<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'R&V Sanjai')</title>

    {{-- Memanggil CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">


    <style>
        /* Definisi Variabel */
        :root {
            --color-primary-start: #ff6b35;
            --color-primary-end: #ffc107;
            --color-dark-start: #2c3e50;
            --color-dark-end: #34495e;
            --color-light: #fff;
            --color-accent: #ff4757;
            --wa-green: #25d366;
        }

        /* Global & Base */
        *{font-family:'Poppins',sans-serif}
        body{background:linear-gradient(135deg,#f5f7fa 0%,#c3cfe2 100%);min-height:100vh;display:flex;flex-direction:column}
        main{min-height:calc(100vh - 200px)}

        /* Navbar Responsif */
        .navbar{
            background:linear-gradient(135deg,var(--color-primary-start) 0%,#f7931e 50%,var(--color-primary-end) 100%)!important;
            box-shadow:0 4px 15px rgba(255,107,53,.3);
            transition:all .3s ease
        }
        .navbar-brand{font-size:1.8rem!important;font-weight:800!important;color:var(--color-light)!important;text-shadow:2px 2px 4px rgba(0,0,0,.3);transition:transform .3s}

        .brand-logo{
            width:70px;
            height:70px;
            object-fit:contain;
            padding:0;
            background:none;
            box-shadow:none;
            border-radius:0;
        }

        .navbar-nav .nav-link{color:var(--color-light)!important;font-weight:500!important;margin:0 5px;padding:10px 15px!important;border-radius:25px;transition:all .3s ease}
        .navbar-nav .nav-link:hover{background:rgba(255,255,255,.2);transform:translateY(-2px)}
        .navbar-nav .nav-link.active{background:rgba(255,255,255,.3);box-shadow:inset 0 2px 4px rgba(0,0,0,.2)}

        /* Cart & Badges */
        .cart-link{position:relative;display:inline-block}
        .cart-badge{
            position:absolute;top:-8px;right:-8px;background:var(--color-accent);
            color:white;border-radius:50%;min-width:20px;height:20px;
            font-size:.75rem;display:none;align-items:center;justify-content:center;
            animation:pulse 2s infinite;font-weight:600;border:2px solid var(--color-light)
        }
        @keyframes pulse{0%{transform:scale(1)}50%{transform:scale(1.1)}100%{transform:scale(1)}}

        /* Mobile Controls */
        .navbar-toggler{border:none;padding:8px;background:rgba(255,255,255,.1);border-radius:8px}
        .mobile-controls{display:flex;align-items:center;gap:10px}
        .icon-wrapper{background:rgba(255,255,255,.15);border-radius:12px;padding:8px 12px;transition:all .3s ease;color:white}

        /* --- FOOTER STYLES --- */
        footer {
            background: linear-gradient(135deg, var(--color-dark-start) 0%, var(--color-dark-end) 100%) !important;
            color: white;
            padding-top: 50px;
            margin-top: auto;
        }
        .footer-title {
            font-weight: 700;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 10px;
        }
        .footer-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background: var(--color-primary-start);
        }
        .footer-link {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: 0.3s;
            display: block;
            margin-bottom: 12px;
        }
        .footer-link:hover {
            color: var(--color-primary-end);
            padding-left: 8px;
        }
        .social-circle {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: white;
            transition: 0.3s;
            margin-right: 10px;
            text-decoration: none;
        }
        .social-circle:hover {
            background: var(--color-primary-start);
            transform: translateY(-5px);
            color: white;
        }
        .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 15px;
            color: rgba(255,255,255,0.7);
        }
        .contact-item i {
            color: var(--color-primary-end);
            margin-top: 5px;
        }

        /* --- WA Widget & Scroll Top --- */
        .floating-controls { position: fixed; bottom: 20px; right: 20px; z-index: 1050; display: flex; flex-direction: column; align-items: flex-end; gap: 15px; }

        /* Animasi Tombol WA */
        .wa-button {
            width: 60px; height: 60px; background-color: var(--wa-green);
            color: white; border-radius: 50%; display: flex; align-items: center;
            justify-content: center; font-size: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            cursor: pointer; transition: all .3s ease;
            position: relative; z-index: 10;
        }

        /* Efek Gelombang/Ripple Pulse */
        .wa-button::before {
            content: '';
            position: absolute;
            width: 100%; height: 100%;
            background-color: var(--wa-green);
            border-radius: 50%;
            z-index: -1;
            animation: pulse-wa 2s infinite;
        }

        @keyframes pulse-wa {
            0% { transform: scale(1); opacity: 0.8; }
            100% { transform: scale(1.6); opacity: 0; }
        }

        .wa-button:hover {
            transform: scale(1.1);
            background-color: #20ba5a;
        }

        /* Label Muncul saat Hover atau Delay */
        .wa-label {
            position: absolute; right: 75px; background: white; color: #333;
            padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); white-space: nowrap;
            opacity: 0; transform: translateX(10px); transition: 0.3s; pointer-events: none;
        }
        .wa-container:hover .wa-label { opacity: 1; transform: translateX(0); }

        .wa-chat-box {
            position: absolute; bottom: 80px; right: 0; width: 300px;
            background: white; border-radius: 15px; overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            display: none; flex-direction: column;
            transform-origin: bottom right;
            animation: scaleIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.5); }
            to { opacity: 1; transform: scale(1); }
        }

        .wa-chat-header { background: var(--wa-green); color: white; padding: 15px; }
        .wa-chat-body { padding: 15px; background: #f0f2f5; }
        .chat-option {
            background: white; border-radius: 10px; padding: 12px; display: flex; align-items: center;
            text-decoration: none; color: #333; margin-bottom: 8px; transition: 0.2s; border: 1px solid #eee; gap: 10px;
        }
        .chat-option:hover { transform: translateX(-5px); background: #f9f9f9; border-color: var(--wa-green); }
        .chat-option i { color: var(--wa-green); }

        .scroll-top {
            width: 45px; height: 45px; background: var(--color-light);
            color: var(--color-primary-start); border: 2px solid var(--color-primary-start);
            border-radius: 50%; font-size: 1.1rem; cursor: pointer; opacity: 0;
            visibility: hidden; transition: all .3s ease;
        }
        .scroll-top.show { opacity: 1; visibility: visible; }

        @media (max-width:991.98px){
            .navbar-brand{font-size:1.5rem!important}
            .brand-logo{width:50px;height:50px}
            .navbar-nav{background:rgba(255,255,255,.1);border-radius:15px;margin-top:15px;padding:15px}
            footer { text-align: center; }
            .footer-title::after { left: 50%; transform: translateX(-50%); }
            .contact-item { justify-content: center; }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            {{-- Logo --}}
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('beranda') }}">
                <img src="{{ asset('asset/logo2.png') }}" alt="Logo" class="brand-logo">
                <div class="d-none d-sm-block">
                    R&V Sanjai
                    <small style="font-size:.6rem;display:block;font-weight:400;margin-top:-5px">Keripik Khas Minang</small>
                </div>
            </a>

            {{-- Mobile Controls --}}
            <div class="mobile-controls d-lg-none">
                <a href="{{ route('keranjang.index') }}" class="icon-wrapper text-decoration-none cart-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge" id="cartBadgeMobile"></span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    @php
                        $menus = [
                            ['beranda', 'fas fa-home', 'Home'],
                            ['tentang', 'fas fa-info-circle', 'Tentang'],
                            ['produk', 'fas fa-box', 'Produk'],
                            ['pesanan.saya', 'fas fa-shopping-bag', 'Pesanan'],
                        ];
                        $currentRoute = Route::currentRouteName();
                    @endphp

                    @foreach ($menus as $menu)
                        <li class="nav-item">
                            <a class="nav-link{{ $menu[0] == $currentRoute ? ' active' : '' }}" href="{{ route($menu[0]) }}">
                                <i class="{{ $menu[1] }} me-1"></i> {{ $menu[2] }}
                            </a>
                        </li>
                    @endforeach

                    <li class="nav-item d-none d-lg-block">
                        <a class="nav-link" href="{{ route('keranjang.index') }}">
                            <div class="cart-link">
                                <i class="fas fa-shopping-cart me-1"></i>
                                <span class="cart-badge" id="cartBadge"></span>
                            </div>
                        </a>
                    </li>

                    <li class="nav-item dropdown ms-lg-2">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> {{ Auth::check() ? explode(' ', Auth::user()->name)[0] : 'Akun' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li><a class="dropdown-item" href="{{ Auth::check() ? route('profile') : route('login') }}">
                                <i class="fas fa-user me-2 small"></i> {{ Auth::check() ? 'Profil Saya' : 'Login / Register' }}
                            </a></li>
                            @auth
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2 small"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            @endauth
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer>
        <div class="container">
            <div class="row g-4">
                {{-- Kolom 1: Profil --}}
                <div class="col-lg-4 col-md-6">
                    <h5 class="text-white fw-bold mb-3">R&V SANJAI</h5>
                    <p class="text-white-50 small" style="line-height: 1.8;">
                        Pelopor oleh-oleh khas Minang dengan cita rasa autentik. Kami menghadirkan keripik sanjai pilihan dengan bumbu tradisional yang diolah secara higienis untuk menjaga kualitas rasa terbaik dari Ranah Minang.
                    </p>
                    <div class="mt-4">
                        <a href="#" class="social-circle"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-circle"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://wa.me/6285165755238" class="social-circle"><i class="fab fa-whatsapp"></i></a>
                        <a href="#" class="social-circle"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>

                {{-- Kolom 2: Tautan Cepat --}}
                <div class="col-lg-2 col-md-6">
                    <h6 class="footer-title">Navigasi</h6>
                    <a href="{{ route('beranda') }}" class="footer-link">Home</a>
                    <a href="{{ route('produk') }}" class="footer-link">Semua Produk</a>
                    <a href="{{ route('tentang') }}" class="footer-link">Tentang Kami</a>
                    <a href="{{ route('pesanan.saya') }}" class="footer-link">Lacak Pesanan</a>
                </div>

                {{-- Kolom 3: Layanan --}}
                <div class="col-lg-3 col-md-6">
                    <h6 class="footer-title">Bantuan</h6>
                    <a href="#" class="footer-link">Syarat & Ketentuan</a>
                    <a href="#" class="footer-link">Kebijakan Privasi</a>
                    <a href="#" class="footer-link">Cara Pemesanan</a>
                    <a href="#" class="footer-link">FAQ</a>
                </div>

                {{-- Kolom 4: Kontak --}}
                <div class="col-lg-3 col-md-6">
                    <h6 class="footer-title">Kontak Kami</h6>
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span class="small">Lubuk Minturun, Kota Padang, Sumatera Barat</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone-alt"></i>
                        <span class="small">+62 851-6575-5238</span>
                    </div>
                </div>
            </div>

            <hr class="mt-5 mb-4" style="opacity: 0.1; background: white;">

            <div class="row align-items-center pb-4">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 text-white-50 small">&copy; {{ date('Y') }} <strong>R&V Sanjai</strong>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                    <img src="https://i.ibb.co/vX8p8fS/payments.png" alt="Metode Pembayaran" style="height: 25px; filter: grayscale(1) brightness(2);">
                </div>
            </div>
        </div>
    </footer>

    {{-- Floating Controls --}}
    <div class="floating-controls">
        <button class="scroll-top" id="scrollTop"><i class="fas fa-chevron-up"></i></button>

        <div class="wa-container" style="position: relative;">
            {{-- Label WhatsApp --}}
            <div class="wa-label">Ada yang bisa dibantu?</div>

            {{-- Chat Box --}}
            <div class="wa-chat-box" id="waChatBox">
                <div class="wa-chat-header text-center">
                    <h6 class="mb-0 fw-bold">Admin R&V Sanjai</h6>
                    <small><i class="fas fa-circle text-light me-1" style="font-size: 8px;"></i> Online | Respon Cepat</small>
                </div>
                <div class="wa-chat-body">
                    <a href="https://wa.me/6285165755238?text=Halo%20Admin%2C%20tanya%20stok%20untuk%20produk..." target="_blank" class="chat-option">
                        <i class="fas fa-box"></i><span>Tanya Stok Produk</span>
                    </a>
                    <a href="https://wa.me/6285165755238?text=Halo%20Admin%2C%20saya%20ingin%20cek%20status%20pesanan%20saya..." target="_blank" class="chat-option">
                        <i class="fas fa-truck"></i><span>Cek Status Pesanan</span>
                    </a>
                    <a href="https://wa.me/6285165755238?text=Halo%20Admin%2C%20saya%20tertarik%20menjadi%20reseller..." target="_blank" class="chat-option">
                        <i class="fas fa-handshake"></i><span>Gabung Reseller</span>
                    </a>
                </div>
            </div>

            {{-- Tombol Utama --}}
            <div class="wa-button" onclick="toggleWaChat()">
                <i class="fab fa-whatsapp"></i>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const sBtn = document.getElementById('scrollTop');
        const bD = document.getElementById('cartBadge');
        const bM = document.getElementById('cartBadgeMobile');
        const navEl = document.querySelector('.navbar');

        function toggleWaChat() {
            const chatBox = document.getElementById('waChatBox');
            const isFlex = chatBox.style.display === 'flex';
            chatBox.style.display = isFlex ? 'none' : 'flex';
        }

        // Tutup chat box jika klik di luar
        window.addEventListener('click', (e) => {
            const controls = document.querySelector('.wa-container');
            if (controls && !controls.contains(e.target)) {
                document.getElementById('waChatBox').style.display = 'none';
            }
        });

        function updateBadgeMarkup(jumlah) {
            const count = parseInt(jumlah) || 0;
            const display = count > 0 ? 'flex' : 'none';
            [bD, bM].forEach(badge => { if(badge) { badge.textContent = count; badge.style.display = display; } });
            localStorage.setItem('cartCount', count);
        }

        async function refreshCartFromServer() {
            try {
                const response = await fetch("{{ route('keranjang.count') }}", { headers: { 'Accept': 'application/json' } });
                const data = await response.json();
                updateBadgeMarkup(data.count);
            } catch (error) { updateBadgeMarkup(localStorage.getItem('cartCount') || 0); }
        }

        document.addEventListener('DOMContentLoaded', () => {
            refreshCartFromServer();
            window.addEventListener('scroll', () => {
                const y = window.pageYOffset;
                y > 300 ? sBtn.classList.add('show') : sBtn.classList.remove('show');
                navEl.style.background = y > 50
                    ? 'linear-gradient(135deg,rgba(255,107,53,.95) 0%,rgba(247,147,30,.95) 50%,rgba(255,193,7,.95) 100%)'
                    : 'linear-gradient(135deg,#ff6b35 0%,#f7931e 50%,#ffc107 100%)';
            });
        });

        sBtn.addEventListener('click', () => window.scrollTo({top:0, behavior:'smooth'}));
        document.addEventListener('cartUpdated', (e) => updateBadgeMarkup(e.detail.count));
    </script>
</body>
</html>
