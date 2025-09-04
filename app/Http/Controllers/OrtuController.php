<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Ortu;
use App\Models\User;
use App\Models\Admin;
use App\Models\JadwalKegiatan;
use App\Models\Laporan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class OrtuController extends Controller
{
    //
    public function dashboard()
    {
        return view('ortu.dashboard');
    }

    public function showLaporan()
    {
        if (!session()->has('role')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userId = session('user_id');
        $role = session('role');

        $profile = DB::table('users')
            ->leftJoin('santri', 'santri.user_id', '=', 'users.id')
            ->leftJoin('ortu', 'ortu.user_id', '=', 'users.id')
            ->where('users.id', $userId)
            ->select(
                'users.id as user_id',
                'users.username',
                DB::raw('COALESCE(santri.nama, ortu.nama) as nama'),
                DB::raw('COALESCE(santri.foto, ortu.foto) as foto'),
                'santri.id as santri_id',
                'ortu.santri_id as santri_id_ortu'
            )
            ->first();

        // Ambil santri_id yang sesuai
        $santriId = null;
        if ($role === 'SANTRI') {
            $santriId = $profile->santri_id;
        } elseif ($role === 'ORANG TUA') {
            $santriId = $profile->santri_id_ortu;
        }

        if (!$santriId) {
            return redirect()->back()->with('error', 'Data santri tidak ditemukan.');
        }

        // Ambil semua jadwal
        $jadwals = JadwalKegiatan::orderBy('tanggal', 'DESC')
            ->orderBy('waktu_mulai', 'DESC')
            ->get();

        // Ambil laporan berdasarkan santri_id
        $laporans = Laporan::where('santri_id', $santriId)
            ->get()
            ->keyBy('jadwal_id');

        return view('ortu.laporan', compact('role', 'profile', 'jadwals', 'laporans'));
    }

    public function typeLaporan(Request $request)
    {
        if (!session()->has('role')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userId = session('user_id');

        $profile = DB::table('users')
            ->join('ortu', 'ortu.user_id', '=', 'users.id') // join ortu
            ->join('santri', 'santri.id', '=', 'ortu.santri_id') // ambil santri berdasarkan ortu
            ->where('users.id', $userId)
            ->select(
                'users.id as user_id',
                'users.username',
                'santri.nama',
                'santri.foto',
                'santri.id as santri_id'
            )
            ->first();


        $santriId = $profile->santri_id;

        $role = session('role');
        $jadwals = JadwalKegiatan::orderBy('tanggal', 'DESC')
            ->orderBy('waktu_mulai', 'DESC')
            ->get();

        $laporans = Laporan::where('santri_id', $santriId)->get()->keyBy('jadwal_id');

        $type = $request->query('type');
        if ($type === 'uploaded') {
            $jadwals = $jadwals->filter(fn($jadwal) => isset($laporans[$jadwal->id]));
        } elseif ($type === 'not_uploaded') {
            $jadwals = $jadwals->filter(fn($jadwal) => !isset($laporans[$jadwal->id]));
        }

        return view('ortu.upload-tidakLaporan', compact('role', 'profile', 'jadwals', 'laporans', 'type'));
    }
}
