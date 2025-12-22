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
            --bg-light: #fff7ef;
            --white: #ffffff;
            --text-dark: #2d2d2d;
        }

        body {
            background: var(--bg-light);
            font-family: 'Segoe UI', sans-serif;
            color: var(--text-dark);
        }

        /* ===== HEADER ===== */
        .top-gradient {
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            padding: 18px 28px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom-left-radius: 25px;
            border-bottom-right-radius: 25px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 260px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            padding: 25px 0;
            position: fixed;
            top: 110px;
            left: 25px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 13px 25px;
            text-decoration: none;
            color: var(--text-dark);
            transition: .3s;
        }

        .sidebar a i {
            margin-right: 12px;
            color: var(--primary);
        }

        .sidebar a.active {
            background: var(--primary);
            color: white;
            border-radius: 12px;
            margin: 5px 15px;
        }

        /* ===== CONTENT ===== */
        .content-wrapper {
            margin-left: 310px;
            margin-top: 30px;
            padding: 20px;
        }

        .content-card {
            background: white;
            border-radius: 22px;
            padding: 35px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        }

        @media(max-width: 768px) {
            .sidebar { display: none; }
            .content-wrapper { margin-left: 0; }
        }
    </style>
</head>

<body>

<!-- HEADER -->
<div class="top-gradient">
    <h1>Admin R&V Sanjai</h1>

    <!-- HANYA NAMA USER (TANPA LOGOUT) -->
    <span class="fw-semibold">
        {{ auth()->user()->name }}
    </span>
</div>

<!-- SIDEBAR -->
<div class="sidebar d-none d-md-block">

    <div class="text-center fw-bold mb-3 text-warning">Admin Menu</div>

    <a href="{{ route('admin.dashboard') }}"
       class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>

    <a href="{{ route('admin.products.index') }}"
       class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
        <i class="fas fa-box"></i> Kelola Produk
    </a>

    <a href="{{ route('admin.transactions') }}"
       class="{{ request()->routeIs('admin.transactions') ? 'active' : '' }}">
        <i class="fas fa-shopping-cart"></i> Pesanan
    </a>

    <a href="{{ route('admin.laporan.index') }}"
       class="{{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
        <i class="fas fa-chart-line"></i> Laporan
    </a>

    <!-- SATU-SATUNYA LOGOUT -->
    <form action="{{ route('logout') }}" method="POST" class="mt-4 px-3">
        @csrf
        <button type="submit" class="btn btn-danger w-100 rounded-pill">
            <i class="fas fa-power-off me-2"></i> Logout
        </button>
    </form>

</div>

<!-- CONTENT -->
<div class="content-wrapper">
    <div class="content-card">
        <h2>@yield('title')</h2>
        @yield('content')
    </div>
</div>

</body>
</html>
