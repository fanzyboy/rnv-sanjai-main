<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Mengarahkan pengguna ke halaman otentikasi Google.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Menangani callback dari Google setelah otentikasi.
     */
    public function handleGoogleCallback()
{
    try {
        $googleUser = Socialite::driver('google')->user();
        $user = User::where('google_id', $googleUser->getId())->first();

        if ($user) {
            Auth::login($user);
            return redirect()->intended('/dashboard');
        } else {
            $existingUser = User::where('email', $googleUser->getEmail())->first();
            
            if ($existingUser) {
                // Update google_id jika email sudah ada tapi google_id kosong
                $existingUser->update(['google_id' => $googleUser->getId()]);
                Auth::login($existingUser);
                return redirect()->intended('/dashboard');
            }

            // Buat pengguna baru
            $newUser = User::create([
                'name'      => $googleUser->getName(),
                'email'     => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password'  => bcrypt(Str::random(16)), // WAJIB: Kasih password random
                'role'      => 'customer',            // WAJIB: Sesuaikan dengan default role lo
            ]);

            Auth::login($newUser);
            return redirect()->intended('/dashboard');
        }

    } catch (\Exception $e) {
        // Log error buat debugging (cek di storage/logs/laravel.log)
        \Log::error($e->getMessage());
        return redirect('/login')->with('error', 'Gagal login: ' . $e->getMessage());
    }
}
}