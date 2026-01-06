<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            Log::info('Google user', [
                'id' => $googleUser->getId(),
                'email' => $googleUser->getEmail(),
            ]);

            $user = User::where('google_id', $googleUser->getId())->first();

            if (!$user) {
                $user = User::where('email', $googleUser->getEmail())->first();

                if ($user) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                    ]);
                } else {
                    $user = User::create([
                        'name'      => $googleUser->getName() ?? 'User',
                        'email'     => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'password'  => null,
                        'role'      => 'user',
                    ]);
                }
            }

            Auth::login($user);

            return redirect('/');

        } catch (\Exception $e) {
            Log::error('Google Login ERROR', [
                'error' => $e->getMessage(),
            ]);

            return redirect('/login')->with('error', 'Login Google gagal');
        }
    }
}
