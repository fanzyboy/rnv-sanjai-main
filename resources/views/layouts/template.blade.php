<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin R&V Sanjai</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">


    <style>
        :root {
            --primary: #ff6b35;
            --primary-light: #ffc107;
            --bg-light: #fff7ef;
            --white: #ffffff;
            --text-dark: #2d2d2d;
        }

        body {
            background: var(--bg-light);
            font-family: 'Segoe UI', sans-serif;
            color: var(--text-dark);
            margin-bottom: 70px;
        }

        /* ===== FIX PAGINATION (TAMBAHKAN INI) ===== */
        /* Mencegah SVG Laravel/Tailwind menjadi raksasa */
        nav svg {
            max-height: 20px;
            width: auto;
            display: inline-block;
        }

        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            gap: 5px;
            padding: 20px 0;
        }

        /* Gaya tombol pagination agar sesuai tema orange */
        .page-item .page-link {
            border: none;
            background: white;
            color: var(--text-dark);
            border-radius: 8px !important;
            padding: 8px 16px;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .page-item.active .page-link {
            background-color: var(--primary) !important;
            color: white !important;
        }
        /* =========================================== */

        /* ===== HEADER ===== */
        .top-gradient {
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            padding: 15px 25px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.2);
        }

        .top-gradient h1 {
            font-size: 1.2rem;
            margin: 0;
            font-weight: 700;
        }

        /* ===== SIDEBAR (DESKTOP ONLY) ===== */
        .sidebar {
            width: 260px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            padding: 25px 0;
            position: fixed;
            top: 100px;
            left: 25px;
            height: calc(100vh - 130px);
            overflow-y: auto;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 13px 25px;
            text-decoration: none;
            color: var(--text-dark);
            transition: .3s;
            margin: 5px 15px;
            border-radius: 12px;
        }

        .sidebar a i {
            margin-right: 12px;
            color: var(--primary);
            width: 20px;
            text-align: center;
        }

        .sidebar a.active {
            background: var(--primary);
            color: white;
        }

        .sidebar a.active i {
            color: white;
        }

        /* ===== BOTTOM NAV (MOBILE ONLY) ===== */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            box-shadow: 0 -5px 20px rgba(0,0,0,0.1);
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            z-index: 1000;
        }

        .bottom-nav a {
            text-decoration: none;
            color: #a0a0a0;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 0.7rem;
            font-weight: 600;
            transition: 0.3s;
        }

        .bottom-nav a i {
            font-size: 1.3rem;
            margin-bottom: 4px;
        }

        .bottom-nav a.active {
            color: var(--primary);
        }

        /* ===== CONTENT ===== */
        .content-wrapper {
            margin-left: 310px;
            margin-top: 20px;
            padding: 15px;
            transition: 0.3s;
        }

        .content-card {
            background: white;
            border-radius: 22px;
            padding: 25px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.05);
            min-height: 70vh;
        }

        /* ===== RESPONSIVE STRATEGY ===== */
        @media(max-width: 992px) {
            .sidebar { display: none !important; }
            .content-wrapper { margin-left: 0; }
            body { margin-bottom: 80px; }
        }

        @media(min-width: 993px) {
            .bottom-nav { display: none !important; }
        }

        .mobile-logout {
            display: none;
        }

        @media(max-width: 768px) {
            .mobile-logout {
                display: block;
            }
            .desktop-user-name {
                display: none;
            }
        }
    </style>
</head>

<body>

<div class="top-gradient">
    <h1><i class="fas fa-store me-2"></i>Admin R&V</h1>

    <div class="d-flex align-items-center">
        <span class="fw-semibold desktop-user-name me-3 text-white">
            {{ auth()->user()->name }}
        </span>

        <form action="{{ route('logout') }}" method="POST" class="mobile-logout">
            @csrf
            <button type="submit" class="btn btn-sm btn-light text-danger rounded-circle">
                <i class="fas fa-power-off"></i>
            </button>
        </form>
    </div>
</div>

<div class="sidebar d-none d-lg-block">
    <div class="text-center fw-bold mb-3 text-warning">Admin Menu</div>

    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>

    <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
        <i class="fas fa-box"></i> Produk
    </a>

    <a href="{{ route('admin.transactions') }}" class="{{ request()->routeIs('admin.transactions') ? 'active' : '' }}">
        <i class="fas fa-shopping-cart"></i> Pesanan
    </a>

    <a href="{{ route('admin.laporan.index') }}" class="{{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
        <i class="fas fa-chart-line"></i> Laporan
    </a>

    <form action="{{ route('logout') }}" method="POST" class="mt-4 px-3">
        @csrf
        <button type="submit" class="btn btn-danger w-100 rounded-pill shadow-sm">
            <i class="fas fa-power-off me-2"></i> Logout
        </button>
    </form>
</div>

<nav class="bottom-nav d-lg-none">
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i>
        <span>Dash</span>
    </a>
    <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
        <i class="fas fa-box"></i>
        <span>Produk</span>
    </a>
    <a href="{{ route('admin.transactions') }}" class="{{ request()->routeIs('admin.transactions') ? 'active' : '' }}">
        <i class="fas fa-shopping-cart"></i>
        <span>Pesanan</span>
    </a>
    <a href="{{ route('admin.laporan.index') }}" class="{{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
        <i class="fas fa-chart-line"></i>
        <span>Laporan</span>
    </a>
</nav>

<div class="content-wrapper">
    <div class="content-card">
        <h4 class="mb-4 border-bottom pb-2">@yield('title')</h4>
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
