<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin R&V Sanjai</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        :root {
            --primary: #ff6b35;
            --primary-light: #ffc107;
            --primary-soft: #ffede3;
            --text-dark: #2d2d2d;
            --bg-light: #fff7ef;
            --white: #ffffff;
            --radius-large: 22px;
        }

        body {
            background: var(--bg-light);
            font-family: 'Segoe UI', sans-serif;
            color: var(--text-dark);
        }

        /* ===== HEADER (GRADIENT ORANGE) ===== */
        .top-gradient {
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            padding: 18px 28px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom-left-radius: 25px;
            border-bottom-right-radius: 25px;
            position: sticky;
            top: 0;
            z-index: 999;

            /* ANIMASI GRADIENT */
            background-size: 300% 300%;
            animation: gradientMove 6s ease infinite;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .top-gradient h1 {
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0;
        }

        /* ===== SIDEBAR FLOATING ===== */
        .sidebar {
            width: 260px;
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            padding: 25px 0;
            height: auto;
            position: fixed;
            top: 110px;
            left: 25px;

            /* ANIMASI MASUK */
            opacity: 0;
            transform: translateX(-25px);
            animation: sidebarIn .7s ease forwards .2s;
        }

        @keyframes sidebarIn {
            0% { opacity: 0; transform: translateX(-25px); }
            100% { opacity: 1; transform: translateX(0); }
        }

        .sidebar .logo {
            text-align: center;
            font-size: 1.3rem;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 18px;

            /* ANIMASI LOGO */
            opacity: 0;
            transform: translateY(-10px);
            animation: logoFade .7s ease forwards .4s;
        }

        @keyframes logoFade {
            to { opacity: 1; transform: translateY(0); }
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 13px 25px;
            text-decoration: none;
            font-weight: 500;
            color: var(--text-dark);
            transition: 0.3s;
        }

        /* ANIMASI HOVER */
        .sidebar a:hover {
            padding-left: 35px;
            background: rgba(255, 107, 53, 0.15);
            border-radius: 12px;
        }

        .sidebar a i {
            margin-right: 12px;
            font-size: 1.25rem;
            color: var(--primary);
        }

        .sidebar a.active {
            background: var(--primary);
            color: white !important;
            border-radius: 12px;
            margin: 5px 15px;
        }

        .sidebar a.active i {
            color: white;
        }

        /* ===== CONTENT CARD ===== */
        .content-wrapper {
            margin-left: 310px;
            margin-top: 30px;
            padding: 20px;
        }

        .content-card {
            background: white;
            border-radius: var(--radius-large);
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
            padding: 35px;

            /* ANIMASI CARD FADE */
            opacity: 0;
            transform: translateY(20px);
            animation: cardFade .8s ease forwards .4s;
        }

        @keyframes cardFade {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .content-card:hover {
            transform: translateY(-4px);
            transition: 0.3s ease;
        }

        .content-card h2 {
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            color: white;
            padding: 14px 25px;
            border-radius: 14px;
            font-size: 1.25rem;
            margin-bottom: 30px;
        }

        /* ===== FORM STYLE ===== */
        .form-control, .form-select {
            border-radius: 14px;
            border: 2px solid #ffd8c7;
            padding: 12px 15px;
            transition: 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 6px rgba(255, 107, 53, 0.6) !important;
        }

        /* ===== BUTTON ===== */
        .btn-primary,
        .btn-light {
            transition: 0.25s ease;
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            padding: 12px 25px;
            border-radius: 12px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: var(--primary-light);
            transform: scale(1.05);
        }

        .btn-primary:active {
            transform: scale(0.95);
        }

        .btn-light:hover {
            transform: scale(1.05);
        }

        /* ===== MOBILE NAV ===== */
        @media(max-width: 768px) {
            .sidebar {
                display: none;
            }

            .content-wrapper {
                margin-left: 0;
                margin-top: 20px;
            }

            .bottom-nav {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                background: white;
                display: flex;
                justify-content: space-around;
                padding: 10px 0;
                box-shadow: 0 -3px 12px rgba(0,0,0,0.1);

                /* FADE IN */
                animation: fadeIn 0.6s ease forwards;
            }

            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(15px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .bottom-nav a {
                text-align: center;
                color: var(--primary);
                font-size: 0.8rem;
                text-decoration: none;
            }

            .bottom-nav a i {
                font-size: 1.4rem;
                margin-bottom: 3px;
            }
        }
    </style>

</head>

<body>

    <!-- HEADER -->
    <div class="top-gradient">
        <h1>Admin R&V Sanjai</h1>
        <button class="btn btn-light rounded-pill px-3">
            <i class="fa fa-user-cog me-2"></i> Pengaturan
        </button>
    </div>

    <!-- SIDEBAR -->
    <div class="sidebar d-none d-md-block">

        <div class="logo">Admin R&V Sanjai</div>

        <a href="{{ route('admin.dashboard') }}"
           class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
           <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>

        <a href="{{ route('admin.products.index') }}"
           class="{{ request()->routeIs('admin.products.index') ? 'active' : '' }}">
           <i class="fas fa-box"></i> Kelola Produk
        </a>

        <a href="{{ route('admin.transactions') }}"
           class="{{ request()->routeIs('admin.transactions') ? 'active' : '' }}">
           <i class="fas fa-shopping-cart"></i> Pesanan
        </a>

        <a href="{{ route('admin.laporan.index') }}"
   class="{{ request()->routeIs('admin.laporan.index') ? 'active' : '' }}">
   <i class="fas fa-chart-line"></i> Laporan
</a>

    </div>

    <!-- MAIN CONTENT -->
    <div class="content-wrapper">
        <div class="content-card">
            <h2>@yield('title')</h2>

            @yield('content')
        </div>
    </div>

    <!-- MOBILE NAV -->
    <div class="bottom-nav d-md-none">
        <a href="{{ route('admin.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i>
            <div>Dashboard</div>
        </a>

        <a href="{{ route('admin.products.index') }}">
            <i class="fas fa-box"></i>
            <div>Produk</div>
        </a>

        <a href="{{ route('admin.transactions') }}">
            <i class="fas fa-shopping-cart"></i>
            <div>Pesanan</div>
        </a>

        <a href="#">
            <i class="fas fa-chart-line"></i>
            <div>Laporan</div>
        </a>
    </div>

</body>
</html>
