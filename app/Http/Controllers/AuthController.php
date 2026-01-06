<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * 🔹 Tampilkan form login
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        return view('auth.login');
    }

    /**
     * 🔹 Proses login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return $this->redirectByRole(Auth::user()->role)
                         ->with('success', 'Berhasil login!');
        }

        return back()
            ->with('error', 'Email atau password salah!')
            ->withInput($request->only('email'));
    }

    /**
     * 🔹 Logout user
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Berhasil logout!');
    }

    /**
     * 🔹 Tampilkan form register
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * 🔹 Proses register
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // default user
        ]);

        return redirect()->route('login')
            ->with('success', 'Akun berhasil dibuat! Silakan login.');
    }

    /**
     * 🔹 Redirect berdasarkan role
     */
    private function redirectByRole($role)
    {
        switch ($role) {
            case 'admin':
                return redirect()->route('admin.dashboard');

            case 'user':
            default:
                return redirect()->route('produk'); // halaman user
        }
    }

    /**
     * 🔹 Profil user
     */
   public function profile()
{
    $user = Auth::user();
    return view('auth.profile', compact('user'));
}

/**
 * 🔹 Update profil user
 */
public function updateProfile(Request $request)
    {
        $user = Auth::user();
$request->validate([
    'name'             => 'required|string|max:100',
    'email'            => 'required|email|unique:users,email,' . $user->id,
    'alamat'           => 'nullable|string|max:255',
    'latitude'         => 'nullable|numeric',
    'longitude'        => 'nullable|numeric',
    'no_hp'            => 'nullable|string|max:20',
    'nomor_rekening'   => 'nullable|string|max:30',
    'nama_bank'        => 'nullable|string|max:50',
    'nama_bank_manual' => 'nullable|string|max:50',

    'provinsi_id'      => 'nullable',
    'kabupaten_id'     => 'nullable',
    'kecamatan_id'     => 'nullable',
    'desa_kelurahan'   => 'nullable|string|max:100',

    'password' => ['nullable', 'confirmed', Password::min(8)],

    'current_password' => [
        function ($attribute, $value, $fail) use ($user, $request) {

            // 🔹 USER LOGIN VIA GOOGLE & BELUM PUNYA PASSWORD
            if ($user->google_id && !$user->password) {
                return;
            }

            // 🔹 USER MANUAL: wajib isi password lama kalau mau ganti password
            if ($request->filled('password') && empty($value)) {
                $fail('Password lama wajib diisi.');
                return;
            }

            // 🔹 Cek kecocokan password lama
            if ($value && !Hash::check(trim($value), $user->password)) {
                $fail('Password saat ini tidak cocok dengan data kami.');
            }
        }
    ],
], [
    'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
]);

        // 1. Logika Nama Bank
        $bankFinal = $request->nama_bank;
        if ($request->nama_bank === 'lainnya') {
            $bankFinal = $request->nama_bank_manual;
        }

        // 2. Siapkan data untuk di-update (Sertakan Wilayah)
        $updateData = [
            'name'           => $request->name,
            'email'          => $request->email,
            'alamat'         => $request->alamat,
            'latitude'       => $request->latitude,
            'longitude'      => $request->longitude,
            'no_hp'          => $request->no_hp,
            'nomor_rekening' => $request->nomor_rekening,
            'nama_bank'      => $bankFinal,
            'provinsi_id'    => $request->provinsi_id,
            'kabupaten_id'   => $request->kabupaten_id,
            'kecamatan_id'   => $request->kecamatan_id,
            'desa_kelurahan' => $request->desa_kelurahan,
        ];

        // 3. Logika Update Password
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make(trim($request->password));
        }

        // 4. Eksekusi Update
        $user->update($updateData);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
