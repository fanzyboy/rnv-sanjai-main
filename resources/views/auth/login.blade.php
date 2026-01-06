@extends('layouts.main')

@section('title', 'Login - R&V Sanjai')

@section('content')

<div class="login-page d-flex align-items-center justify-content-center min-vh-100" style="background: linear-gradient(135deg, #fff5e6 0%, #ffe6cc 100%);">
    <div class="login-card p-5 shadow-lg rounded-4 bg-white" style="max-width: 440px; width: 100%;">
        <div class="text-center mb-4">
            <div class="brand-logo mb-3 p-3 rounded-circle d-inline-block" style="background: linear-gradient(135deg, #ff6b35, #ffc107);">
                <i class="fas fa-box-open fa-3x text-white"></i>
            </div>
            <h3 class="fw-bold mb-2" style="color: #8B4513;">Selamat Datang Kembali!</h3>
            <p class="text-muted">Masuk untuk mulai memesan produk kami</p>
        </div>

        @if (session('error'))
            <div class="alert alert-danger rounded-3 border-0">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold" style="color: #8B4513;">Email</label>
                <div class="input-group">
                    <span class="input-group-text border-end-0 bg-light"><i class="fas fa-envelope" style="color: #ff6b35;"></i></span>
                    <input type="email" name="email" id="email" class="form-control border-start-0 bg-light" placeholder="contoh@email.com" required autofocus>
                </div>
                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold" style="color: #8B4513;">Password</label>
                <div class="input-group">
                    <span class="input-group-text border-end-0 bg-light"><i class="fas fa-lock" style="color: #ff6b35;"></i></span>
                    <input type="password" name="password" id="password" class="form-control border-start-0 bg-light" placeholder="••••••••" required>
                </div>
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember" style="border-color: #ff6b35;">
                    <label class="form-check-label small text-muted" for="remember">Ingat saya</label>
                </div>
                {{-- <a href="#" class="small text-decoration-none fw-semibold" style="color: #ff6b35;">Lupa password?</a> --}}
            </div>

            <button type="submit" class="btn w-100 py-3 fw-semibold text-white mb-3 rounded-3 border-0"
            <button type="submit" class="btn w-100 py-3 fw-semibold text-white mb-3 rounded-3 border-0"
                style="background: linear-gradient(135deg, #ff6b35, #ffc107); transition: all 0.3s ease;">
                <i class="fas fa-sign-in-alt me-2"></i>Masuk Sekarang
            </button>

            <a href="{{ route('google.redirect') }}" class="btn btn-outline-danger w-100 py-2 rounded-3 mb-4" style="border-width: 2px;">
                <i class="fab fa-google me-2"></i>Masuk dengan Google
            </a>

            <div class="text-center pt-3 border-top">
                <p class="small text-muted mb-0">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="fw-bold text-decoration-none" style="color: #ff6b35;">Daftar sekarang</a>
                </p>
            </div>
        </form>
    </div>
</div>

<style>
.login-card input.form-control:focus {
    border-color: #ff6b35;
    box-shadow: 0 0 0 .2rem rgba(255, 107, 53, .15);
    background-color: #fff;
}
.login-card input.form-control:focus + .input-group-text {
    border-color: #ff6b35;
}
.login-card .btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
    transform: translateY(-2px);
}
.login-card button[type="submit"]:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 107, 53, .4);
}
.login-card {
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.5);
}
</style>
@endsection
