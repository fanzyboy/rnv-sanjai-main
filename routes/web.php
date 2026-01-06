<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PreorderController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminTransactionController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('beranda');
Route::get('/tentang', [HomeController::class, 'about'])->name('tentang');
Route::get('/produk', [HomeController::class, 'produk'])->name('produk');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('produk.show');

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'store'])->name('register.post');

/*
|--------------------------------------------------------------------------
| USER (LOGIN REQUIRED)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');

   Route::middleware('auth')->group(function () {

    Route::get('/keranjang', [KeranjangController::class, 'index'])
        ->name('keranjang.index');

    Route::post('/keranjang', [KeranjangController::class, 'store'])
        ->name('keranjang.tambah');

    Route::delete('/keranjang/{variasi_id}', [KeranjangController::class, 'remove'])
        ->name('keranjang.remove');

    Route::get('/keranjang/count', [KeranjangController::class, 'getCartCount'])
        ->name('keranjang.count');
});

Route::get('/checkout/proses', [CheckoutController::class, 'proses'])
    ->name('checkout.proses');

// ✅ SIMPAN CHECKOUT (POST)
Route::post('/checkout/simpan', [CheckoutController::class, 'prosesCheckout'])
    ->name('checkout.simpan');

    Route::get('/pesanan-saya', [CheckoutController::class, 'pesananSaya'])->name('pesanan.saya');

    Route::post('/rating', [RatingController::class, 'store'])->name('rating.store');
    Route::get('/preorder/{price_id?}', [PreorderController::class, 'create'])->name('preorder.create');
Route::post('/preorder/store', [PreorderController::class, 'store'])->name('preorder.store');

});

/*
|--------------------------------------------------------------------------
| ADMIN (AUTH + ROLE ADMIN)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

        Route::get('/dashboard', [HomeController::class, 'adminDashboard'])
            ->name('admin.dashboard');

        Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
        Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('admin.products.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');

        Route::get('/all-transactions', [AdminTransactionController::class, 'index'])
            ->name('admin.transactions');

        Route::put('/orders/updateStatus/{id}', [AdminTransactionController::class, 'updateOrderStatus'])
            ->name('admin.orders.updateStatus');

        Route::put('/preorders/updateStatus/{id}', [AdminTransactionController::class, 'updatePreorderStatus'])
            ->name('admin.preorders.updateStatus');

        Route::get('/laporan', [AdminReportController::class, 'index'])
            ->name('admin.laporan.index');

        Route::post('/laporan/filter', [AdminReportController::class, 'filter'])
            ->name('admin.laporan.filter');

        Route::get('/laporan/export', [AdminReportController::class, 'export'])
            ->name('admin.laporan.export');
});

/*
|--------------------------------------------------------------------------
| GOOGLE LOGIN
|--------------------------------------------------------------------------
*/
Route::get('/auth/google/redirect', [SocialiteController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback'])->name('google.callback');

Route::get('/laporan/export', [AdminReportController::class, 'export'])
    ->name('admin.laporan.export');
