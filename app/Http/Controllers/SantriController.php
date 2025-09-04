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

class SantriController extends Controller
{
    public function dashboard()
    {
        $userId = session('user_id');
        $profile = DB::table('users')
            ->join('santri', 'santri.user_id', '=', 'users.id')
            ->where('users.id', $userId)
            ->select('users.id as user_id', 'users.username', 'santri.nama', 'santri.foto')
            ->first();
        $role = session('role');


        $totalJadwal = JadwalKegiatan::count();
        $jumlahUpload = Laporan::where('user_id', $userId)->count();
        $jumlahTidakUpload = $totalJadwal - $jumlahUpload;

        return view('santri.dashboard', compact('role', 'profile', 'jumlahUpload', 'jumlahTidakUpload'));
    }
    public function showLaporan()
    {
        if (!session()->has('role')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userId = session('user_id');

        $profile = DB::table('users')
            ->join('santri', 'santri.user_id', '=', 'users.id')
            ->where('users.id', $userId)
            ->select('users.id as user_id', 'users.username', 'santri.nama', 'santri.foto', 'santri.id as santri_id')
            ->first();

        $santriId = $profile->santri_id;

        $role = session('role');
        $jadwals = JadwalKegiatan::whereBetween('tanggal', [
            Carbon::today()->toDateString(),
            Carbon::tomorrow()->toDateString()
        ])->orderBy('tanggal', 'DESC')
            ->orderBy('waktu_mulai', 'DESC')
            ->get();

        $laporans = Laporan::where('santri_id', $santriId)->get()->keyBy('jadwal_id');

        return view('santri.laporan', compact('role', 'profile', 'jadwals', 'laporans'));
    }

    // app/Http/Controllers/LaporanController.php

    public function uploadLaporan(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal_kegiatan,id',
            'keterangan' => 'nullable|string|in:Izin,Sakit,Haid',
            'bukti_laporan' => 'required_without:keterangan|nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $userId = session('user_id');
        $santri = Santri::where('user_id', $userId)->first();
        if (!$santri) {
            return redirect()->route('santri.laporan')->with('error', 'Santri tidak ditemukan.');
        }

        // Siapkan data yang akan disimpan
        $data = [
            'keterangan' => $request->keterangan
        ];

        // Hanya proses dan tambahkan 'bukti_laporan' jika ada file yang diupload
        if ($request->hasFile('bukti_laporan')) {
            $data['bukti_laporan'] = $request->file('bukti_laporan')->store('laporan_santri', 'public');
            // Jika ada file, pastikan keterangan dikosongkan agar tidak duplikat
            $data['keterangan'] = null;
        }

        // Gunakan updateOrCreate dengan data yang sudah disiapkan
        Laporan::updateOrCreate(
            [
                'santri_id' => $santri->id,
                'jadwal_id' => $request->jadwal_id,
            ],
            $data
        );

        return redirect()->route('santri.laporan')->with('success', 'Laporan berhasil disimpan.');
    }


    public function update(Request $request)
    {
        $request->validate([
            'laporan_id' => 'required|exists:laporan,id',
            'keterangan' => 'nullable|string|in:Izin,Sakit,Haid',
            'bukti_laporan' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $laporan = Laporan::find($request->laporan_id);

        // Siapkan data yang akan diupdate
        $data = [
            'keterangan' => $request->keterangan
        ];

        if ($request->hasFile('bukti_laporan')) {
            // Hapus file lama jika ada
            if ($laporan->bukti_laporan && Storage::disk('public')->exists($laporan->bukti_laporan)) {
                Storage::disk('public')->delete($laporan->bukti_laporan);
            }
            $data['bukti_laporan'] = $request->file('bukti_laporan')->store('laporan_santri', 'public');
            $data['keterangan'] = null; // Kosongkan keterangan jika ada upload file baru
        }

        // Khusus untuk Opsi Lain, jika tidak ada file baru diupload
        // pastikan path file lama tidak hilang jika memang ada.
        if ($request->keterangan && !$request->hasFile('bukti_laporan')) {
            // Jika ada keterangan (Izin/Sakit) dan tidak ada file baru,
            // hapus path file lama agar tidak ada file bukti yang nyangkut.
            if ($laporan->bukti_laporan && Storage::disk('public')->exists($laporan->bukti_laporan)) {
                Storage::disk('public')->delete($laporan->bukti_laporan);
            }
            $data['bukti_laporan'] = null;
        }


        $laporan->update($data);

        return back()->with('success', 'Laporan berhasil diperbarui.');
    }

    public function showRekapLaporan()
    {
        if (!session()->has('role')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userId = session('user_id');

        $profile = DB::table('users')
            ->join('santri', 'santri.user_id', '=', 'users.id')
            ->where('users.id', $userId)
            ->select('users.id as user_id', 'users.username', 'santri.nama', 'santri.foto', 'santri.id as santri_id')
            ->first();

        $santriId = $profile->santri_id;

        $role = session('role');
        $jadwals = JadwalKegiatan::orderBy('tanggal', 'DESC')
            ->orderBy('waktu_mulai', 'DESC')
            ->get();


        $laporans = Laporan::where('santri_id', $santriId)->get()->keyBy('jadwal_id');

        return view('santri.rekapLaporan', compact('role', 'profile', 'jadwals', 'laporans'));
    }

    public function typeLaporan(Request $request)
    {
        if (!session()->has('role')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userId = session('user_id');

        $profile = DB::table('users')
            ->join('santri', 'santri.user_id', '=', 'users.id')
            ->where('users.id', $userId)
            ->select('users.id as user_id', 'users.username', 'santri.nama', 'santri.foto', 'santri.id as santri_id')
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

        return view('santri.upload-tidakLaporan', compact('role', 'profile', 'jadwals', 'laporans', 'type'));
    }
}
