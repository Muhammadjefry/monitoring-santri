<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Santri;
use App\Models\Ortu;
use App\Models\JadwalKegiatan;
use App\Models\Laporan;


class LoginController extends Controller
{
    public function dashboard()
    {
        if (!session()->has('role')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userId = session('user_id');

        $profile = DB::table('users')
            ->leftJoin('admin', 'admin.user_id', '=', 'users.id')
            ->leftJoin('santri', 'santri.user_id', '=', 'users.id')
            ->leftJoin('ortu', 'ortu.user_id', '=', 'users.id')
            ->where('users.id', $userId)
            ->select(
                'users.id as user_id',
                'users.username',
                DB::raw('COALESCE(admin.nama, ortu.nama, santri.nama) as nama'),
                DB::raw('COALESCE(admin.foto, ortu.foto, santri.foto) as foto'),
                'santri.id as santri_id',
                'ortu.santri_id as santri_id_ortu'
            )
            ->first();

        $jumlahUpload = 0;
        $jumlahTidakUpload = 0;
        $jumlahSantri = 0;
        $laporanHariIni = 0;



        if (session('role') === 'ADMINISTRATOR') {
            // 1. Ambil jadwal untuk hari ini saja
            $jadwalHariIni = JadwalKegiatan::whereDate('tanggal', now()->toDateString())->first();

            // Jika tidak ada jadwal hari ini, semua data dianggap 0
            if (!$jadwalHariIni) {
                $jumlahUpload = 0;
                $jumlahTidakUpload = 0;
                $jumlahSantri = \App\Models\Santri::count();
                $laporanHariIni = 0;
            } else {
                // 2. Ambil semua santri yang terdaftar
                $santriIds = \App\Models\Santri::pluck('id');

                // 3. Hitung jumlah santri yang sudah upload laporan untuk jadwal hari ini
                $jumlahUpload = Laporan::where('jadwal_id', $jadwalHariIni->id)
                    ->whereIn('santri_id', $santriIds)
                    ->count();

                // 4. Hitung jumlah santri yang belum upload
                $jumlahTidakUpload = $santriIds->count() - $jumlahUpload;

                $jumlahSantri = $santriIds->count();

                $laporanHariIni = $jumlahUpload; // Laporan hari ini sama dengan jumlah upload hari ini
            }
        }


        $santriId = null;

        if (session('role') === 'SANTRI') {
            $santriId = $profile->santri_id;
        } elseif (session('role') === 'ORANG TUA') {
            $santriId = $profile->santri_id_ortu;
        }

        if ($santriId) {
            $tanggalMulai = \App\Models\Santri::find($santriId)->created_at;

            $jadwalIds = JadwalKegiatan::whereDate('tanggal', '>=', $tanggalMulai)
                ->whereDate('tanggal', '<=', now()->toDateString())
                ->pluck('id');

            $laporanIds = Laporan::where('santri_id', $santriId)
                ->whereIn('jadwal_id', $jadwalIds)
                ->pluck('jadwal_id');

            $jumlahUpload = $laporanIds->count();
            $jumlahTidakUpload = $jadwalIds->diff($laporanIds)->count();
        }

        return view('dashboard', compact('profile', 'jumlahUpload', 'jumlahTidakUpload', 'jumlahSantri', 'laporanHariIni'));
    }

    public function showLogin()
    {
        return view('login');
    }

    public function showProfile()
    {
        if (!session()->has('role')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $tableMap = [
            'ADMINISTRATOR' => 'admin',
            'SANTRI' => 'santri',
            'ORANG TUA' => 'ortu'
        ];

        $userId = session('user_id');
        $role = session('role');

        if (!isset($tableMap[$role])) {
            return redirect('/login')->with('error', 'Role tidak dikenali.');
        }

        $tableName = $tableMap[$role];

        $profile = DB::table('users')
            ->join($tableName, "$tableName.user_id", '=', 'users.id')
            ->where('users.id', $userId)
            ->select(
                'users.id as user_id',
                'users.username',
                "$tableName.nama",
                "$tableName.foto"
            )
            ->first();

        if (!$profile) {
            return redirect()->back()->with('error', 'Profil tidak ditemukan.');
        }

        return view('profile', compact('role', 'profile'));
    }

    public function profileUpdate(Request $request)
    {
        $userId = session('user_id');
        $role = session('role');

        if (!$userId || !$role) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $tableMap = [
            'ADMINISTRATOR' => 'admin',
            'SANTRI' => 'santri',
            'ORANG TUA' => 'ortu'
        ];

        if (!isset($tableMap[$role])) {
            return redirect('/login')->with('error', 'Role tidak dikenali.');
        }

        $tableName = $tableMap[$role];

        $request->validate([
            'nama' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password_lama' => 'nullable|string',
            'password' => 'nullable|string|confirmed',
        ]);

        $duplicate = DB::table($tableName)
            ->where('nama', $request->nama)
            ->where('user_id', '!=', $userId)
            ->exists();

        if ($duplicate) {
            return redirect()->route('profile')->with('error', 'Nama sudah digunakan, coba yang lain.');
        }

        if ($request->filled('password')) {
            $user = DB::table('users')->where('id', $userId)->first();
            if (!Hash::check($request->password_lama, $user->password)) {
                return redirect()->route('profile')->with('error', 'Password lama tidak sesuai.');
            }

            DB::table('users')->where('id', $userId)->update([
                'password' => bcrypt($request->password),
            ]);
        }

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store("foto_$tableName", 'public');
        }

        if ($tableName === 'admin') {
            $request->validate(['username' => 'required|string|max:255']);
            DB::table('users')->where('id', $userId)->update([
                'username' => $request->username,
            ]);
        }

        $updateData = ['nama' => $request->nama];
        if ($fotoPath) {
            $updateData['foto'] = $fotoPath;
        }

        DB::table($tableName)->where('user_id', $userId)->update($updateData);

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }


    public function login(Request $request)
    {
        $user = User::where('username', $request->username)
            ->where('role', $request->role)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Username, password, atau role salah!');
        }

        session([
            'user_id' => $user->id,
            'username' => $user->username,
            'role' => $user->role,
        ]);

        return match ($user->role) {
            'ADMINISTRATOR' => redirect('/admin/dashboard'),
            'SANTRI' => redirect('/santri/dashboard'),
            'ORANG TUA' => redirect('/ortu/dashboard'),
            default => redirect('/'),
        };
    }

    public function logout()
    {
        session()->flush();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login');
    }
}
