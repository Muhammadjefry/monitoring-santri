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
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{

    public function showSantri()
    {
        if (!session()->has('role')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userId = session('user_id');
        $profile = DB::table('users')
            ->join('admin', 'admin.user_id', '=', 'users.id')
            ->where('users.id', $userId)
            ->select('users.id as user_id', 'users.username', 'admin.nama', 'admin.foto')
            ->first();
        $role = session('role');

        $santris = Santri::with('user')->get();

        return view('admin.santri', compact('santris', 'role', 'profile'));
    }
    public function showOrtu()
    {
        if (!session()->has('role')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userId = session('user_id');
        $profile = DB::table('users')
            ->join('admin', 'admin.user_id', '=', 'users.id')
            ->where('users.id', $userId)
            ->select('users.id as user_id', 'users.username', 'admin.nama', 'admin.foto')
            ->first();
        $role = session('role');
        $ortu = Ortu::with(['user', 'santri'])->get();
        $santri = Santri::all();

        return view('admin.ortu', compact('ortu', 'santri', 'role', 'profile'));
    }

    // Santri

    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'nama'     => 'required',
            'kamar'    => 'required',
        ]);

        $existingUsername = User::where('username', $request->username)->first();
        $existingNama     = Santri::where('nama', $request->nama)->first();
        $existingKamar    = Santri::where('kamar', $request->kamar)->first();

        if ($existingUsername) {
            return redirect()->route('admin.santri')->with('error', 'Username sudah digunakan. Silakan coba yang lain.');
        }
        if ($existingNama) {
            return redirect()->route('admin.santri')->with('error', 'Nama sudah digunakan. Silakan coba yang lain.');
        }
        if ($existingKamar) {
            return redirect()->route('admin.santri')->with('error', 'Kamar sudah digunakan. Silakan coba yang lain.');
        }
        // Simpan ke tabel users
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role'     => 'SANTRI',
        ]);

        // Simpan ke tabel santri
        Santri::create([
            'user_id' => $user->id,
            'nama'    => $request->nama,
            'kamar'   => $request->kamar,
        ]);

        return redirect()->back()->with('success', 'Santri berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'username' => 'required',
            'nama'     => 'required',
        ]);

        $user = User::findOrFail($id);
        $santri = Santri::where('user_id', $id)->firstOrFail();


        $existingUsername = User::where(function ($query) use ($request) {
            $query->where('username', $request->username);
        })
            ->where('id', '!=', $user->id)
            ->first();

        $existingNama = Santri::where(function ($query) use ($request) {
            $query->where('nama', $request->nama);
        })

            ->where('id', '!=', $santri->id)
            ->first();

        if ($existingUsername || $existingNama) {
            return redirect()->route('admin.santri')
                ->with('error', 'Username atau nama sudah digunakan, silakan coba yang lain.');
        }

        // Update kedua tabel
        $user->update([
            'username' => $request->username,
        ]);

        $santri->update([
            'nama' => $request->nama,
            'kamar' => $request->kamar,
        ]);


        return redirect()->route('admin.santri')->with('success', 'Data berhasil diperbarui.');
    }
    public function destroy(Request $request)
    {
        $id = $request->id;

        $user = User::find($id);

        if (!$user) {
            return redirect()->route('admin.santri')->with('error', 'User tidak ditemukan!');
        }

        $santri = Santri::where('user_id', $user->id)->first();
        if ($santri) {
            $santri->delete();
        }

        $user->delete();

        return redirect()->route('admin.santri')->with('success', 'User dan data terkait berhasil dihapus!');
    }

    public function resetPassword(Request $request)
    {
        $user = User::find($request->id);

        if (!$user) {
            return redirect()->route('admin.santri')->with('error', 'User tidak ditemukan!');
        }

        $user->password = bcrypt('123');
        $user->save();

        return redirect()->route('admin.santri')->with('success', 'Password berhasil di-reset menjadi 123.');
    }


    // Ortu

    public function store_ortu(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'password' => 'required',
            'nama'     => 'required',
            'santri_id' => 'required|exists:santri,id',

        ]);

        // Validasi manual untuk nomor HP
        $noHp = $request->no_hp;

        if (empty($noHp)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'No HP wajib diisi.');
        }

        if (!preg_match('/^08[1-9][0-9]{7,10}$/', $noHp)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Format No HP tidak valid. Contoh: 081234567890.');
        }

        // Simpan ke tabel users
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role'     => 'ORANG TUA',
        ]);

        // Simpan ke tabel ortu
        Ortu::create([
            'user_id' => $user->id,
            'nama'    => $request->nama,
            'no_hp'    => $request->no_hp,
            'santri_id'  => $request->santri_id,
        ]);

        return redirect()->back()->with('success', 'Santri berhasil ditambahkan!');
    }

    public function update_ortu(Request $request, $id)
    {
        $request->validate([
            'username'   => 'required',
            'nama'       => 'required',
            'no_hp'      => 'required',
            'santri_id'  => 'required|exists:santri,id',
        ]);

        $user = User::findOrFail($id);
        $ortu = Ortu::where('user_id', $id)->firstOrFail();

        // Cek apakah username sudah digunakan user lain
        $existingUsername = User::where('username', $request->username)
            ->where('id', '!=', $user->id)
            ->first();

        if ($existingUsername) {
            return redirect()->back()->with('error', 'Username sudah digunakan.');
        }

        // Update user
        $user->update([
            'username' => $request->username,
        ]);

        // Update ortu
        $ortu->update([
            'nama'       => $request->nama,
            'no_hp'      => $request->no_hp,
            'santri_id'  => $request->santri_id,
        ]);

        return redirect()->route('admin.ortu')->with('success', 'Data berhasil diperbarui.');
    }


    public function destroy_ortu(Request $request)
    {
        $id = $request->id;

        $user = User::find($id);

        if (!$user) {
            return redirect()->route('admin.ortu')->with('error', 'User tidak ditemukan!');
        }

        $ortu = Ortu::where('user_id', $user->id)->first();
        if ($ortu) {
            $ortu->delete();
        }

        $user->delete();

        return redirect()->route('admin.ortu')->with('success', 'User dan data terkait berhasil dihapus!');
    }

    // Reset password user jadi "123"
    public function resetPassword_ortu(Request $request)
    {
        $user = User::find($request->id);

        if (!$user) {
            return redirect()->route('admin.ortu')->with('error', 'User tidak ditemukan!');
        }

        $user->password = bcrypt('123');
        $user->save();

        return redirect()->route('admin.ortu')->with('success', 'Password berhasil di-reset menjadi 123.');
    }

    public function laporanMasuk(Request $request)
    {
        // ... (kode untuk session dan profile Anda tetap sama) ...
        if (!session()->has('role')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }
        $userId = session('user_id');
        $profile = DB::table('users')
            ->join('admin', 'admin.user_id', '=', 'users.id')
            ->where('users.id', $userId)
            ->select('users.id as user_id', 'users.username', 'admin.nama', 'admin.foto')
            ->first();
        $role = session('role');
        $santris = Santri::with('user')->get();
        $today = Carbon::today();

        $laporans = Laporan::with(['santri.user', 'jadwal'])
            ->whereDate('created_at', $today)
            ->latest('created_at')
            ->get();

        return view('admin.laporan', compact('santris', 'role', 'profile', 'laporans'));
    }


    public function showRekapLaporan()
    {
        if (!session()->has('role')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }
        $userId = session('user_id');
        $role = session('role');

        $profile = DB::table('users')
            ->join('admin', 'admin.user_id', '=', 'users.id')
            ->where('users.id', $userId)
            ->select('users.id as user_id', 'users.username', 'admin.nama', 'admin.foto')
            ->first();

        $santris = DB::table('santri')
            ->join('users', 'santri.user_id', '=', 'users.id')
            ->leftJoin('ortu', 'ortu.santri_id', '=', 'santri.id')
            ->select(
                'santri.id as santri_id',
                'santri.nama as nama',
                'ortu.nama as nama_ortu',
                'ortu.no_hp as no_hp_ortu'
            )
            ->get();

        $today = now();

        $jadwals = JadwalKegiatan::orderBy('tanggal')->orderBy('waktu_mulai')->get();

        $rekap = $santris->map(function ($santri) use ($today, $jadwals) {
            $totalJadwal = $jadwals->count();

            $sudahLapor = 0;
            $tidakLapor = 0;
            $belumLapor = 0;

            foreach ($jadwals as $jadwal) {
                $jadwalDateTimeEnd = Carbon::parse($jadwal->tanggal . ' ' . str_replace('.', ':', $jadwal->waktu_selesai));


                $laporanExists = Laporan::where('santri_id', $santri->santri_id)
                    ->where('jadwal_id', $jadwal->id)
                    ->where(function ($query) {
                        $query->whereNotNull('bukti_laporan')
                            ->orWhereNotNull('keterangan');
                    })
                    ->exists();

                if ($jadwalDateTimeEnd->lt($today)) {
                    if ($laporanExists) {
                        $sudahLapor++;
                    } else {
                        $tidakLapor++;
                    }
                } else {

                    if ($laporanExists) {
                        $sudahLapor++;
                    } else {
                        $belumLapor++;
                    }
                }
            }

            $jadwalSelesai = $sudahLapor + $tidakLapor;
            $persentase = $jadwalSelesai > 0 ? round(($sudahLapor / $jadwalSelesai) * 100, 2) : 0;

            return (object) [
                'santri_id'    => $santri->santri_id,
                'nama'         => $santri->nama,
                'nama_ortu'    => $santri->nama_ortu,
                'no_hp_ortu'   => $santri->no_hp_ortu,
                'total_jadwal' => $totalJadwal,
                'sudah_lapor'  => $sudahLapor,
                'belum_lapor'  => $belumLapor,
                'tidak_lapor'  => $tidakLapor,
                'persentase'   => $persentase,
            ];
        });

        return view('admin.rekapLaporan', compact('role', 'rekap', 'profile'));
    }

    public function showDetailLaporan($santri_id)
    {
        $jadwals = JadwalKegiatan::orderBy('tanggal', 'desc')
            ->orderBy('waktu_mulai', 'desc')
            ->get();

        $laporans = Laporan::where('santri_id', $santri_id)
            ->get()
            ->keyBy('jadwal_id');

        $userId = session('user_id');
        $role = session('role');

        $profile = DB::table('users')
            ->join('admin', 'admin.user_id', '=', 'users.id')
            ->where('users.id', $userId)
            ->select('users.id as user_id', 'users.username', 'admin.nama', 'admin.foto')
            ->first();


        return view('admin.detailLaporan', compact('role', 'profile', 'jadwals', 'laporans'));
    }


    public function sendRekapPdf($santri_id)

    {

        try {

            Log::info("Memulai proses pengiriman PDF untuk Santri ID: {$santri_id}");


            $santri = DB::table('santri')

                ->join('users', 'santri.user_id', '=', 'users.id')

                ->leftJoin('ortu', 'ortu.santri_id', '=', 'santri.id')

                ->select(

                    'santri.id as santri_id',

                    'santri.nama as nama_santri',

                    'ortu.nama as nama_ortu',

                    'ortu.no_hp as no_hp_ortu'

                )

                ->where('santri.id', $santri_id)

                ->first();


            // --- LOG: Mengecek data santri ---

            if (!$santri) {

                Log::warning("Santri dengan ID: {$santri_id} tidak ditemukan.");

                return back()->with('error', 'Santri tidak ditemukan');
            }

            Log::info("Data santri ditemukan: " . json_encode($santri));



            // --- Proses pembuatan PDF ---

            $jadwals = JadwalKegiatan::orderBy('tanggal', 'desc')
                ->orderBy('waktu_mulai', 'desc')
                ->get();

            $laporans = Laporan::where('santri_id', $santri_id)->get()->keyBy('jadwal_id');

            $pdf = Pdf::loadView('admin.rekap', compact('santri', 'jadwals', 'laporans'))

                ->setPaper('a4', 'portrait');



            $namaSantriSlug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '', $santri->nama_santri));

            $filename = 'RekapPdfSantri-' . $namaSantriSlug . '-' . time() . '.pdf';

            $pdf->save(storage_path('app/public/' . $filename));

            Log::info("PDF berhasil dibuat dan disimpan dengan nama: {$filename}");

            $urlPdf = asset('storage/' . $filename);

            // --- Validasi dan Format Nomor HP ---
            $target = preg_replace('/\D+/', '', (string) $santri->no_hp_ortu);
            if (str_starts_with($target, '0')) {
                $target = '62' . substr($target, 1);
                Log::info("Nomor HP diubah dari format '0' menjadi '62'. Nomor baru: {$target}");
            } elseif (!str_starts_with($target, '62')) {
                $target = '62' . $target;
                Log::info("Nomor HP ditambahkan '62'. Nomor baru: {$target}");
            }

            // --- LOG: Mengecek nomor HP setelah diformat ---
            if (empty($target) || strlen($target) < 10) {
                Log::error("Nomor HP orang tua tidak valid atau kosong. Nomor: {$santri->no_hp_ortu}");
                return back()->with('error', 'Nomor HP orang tua tidak valid.');
            }


            // --- Proses pengiriman ke Fonnte ---
            $token = env('FONNTE_TOKEN');
            if (empty($token)) {
                Log::error("TOKEN FONNTE tidak ditemukan di file .env");
                return back()->with('error', 'Token API Fonnte tidak ditemukan.');
            }


            $urlPdf = url('/download-rekap/' . $filename);

            // --- LOG: Cek URL PDF dan data API ---
            Log::info("URL PDF yang akan dikirim: {$urlPdf}");
            Log::info("Token Fonnte yang digunakan: {$token}");
            Log::info("Target nomor WA: {$target}");


            $response = Http::withHeaders([

                'Authorization' => $token

            ])->post('https://api.fonnte.com/send', [

                'target'  => $target,

                'message' => "Assalamu’alaikum, Bapak/Ibu {$santri->nama_ortu} 🙏\n"

                    . "Berikut rekap laporan kegiatan santri: {$santri->nama_santri}.\n\n"

                    . "Unduh PDF di tautan berikut (tekan & tahan lalu salin, tempel di Chrome/Browser):\n"

                    . "{$urlPdf}\n\n"

                    . "Jika tautan belum bisa diklik:\n"

                    . "1) Simpan nomor ini sebagai kontak (misal: Admin Pondok),\n"

                    . "2) Tutup lalu buka kembali WhatsApp,\n"

                    . "3) Atau salin tautan di atas lalu tempel di Chrome/Browser.\n\n"

                    . "Terima kasih. 🙏"

            ]);



            // --- Log Hasil Respons dari Fonnte ---
            if ($response->ok()) {
                Log::info("Pesan berhasil dikirim ke Fonnte. Response: " . $response->body());
                return back()->with('success', 'Link PDF berhasil dikirim ke WA');
            } else {
                Log::error("Gagal mengirim link PDF. Status: " . $response->status() . ". Response Body: " . $response->body());
                return back()->with('error', 'Gagal mengirim link PDF');
            }
        } catch (\Exception $e) {
            // --- LOG: Menangkap semua error lain yang tidak terduga ---
            Log::error("Terjadi error saat mengirim PDF: " . $e->getMessage() . " di baris " . $e->getLine());
            return back()->with('error', 'Terjadi kesalahan sistem. Silakan cek log.');
        }
    }


    public function typeLaporan(Request $request)
    {
        if (!session()->has('role')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }
        $userId = session('user_id');
        $role = session('role');

        $profile = DB::table('users')
            ->join('admin', 'admin.user_id', '=', 'users.id')
            ->where('users.id', $userId)
            ->select('users.id as user_id', 'users.username', 'admin.nama', 'admin.foto')
            ->first();

        $santris = DB::table('santri')
            ->join('users', 'santri.user_id', '=', 'users.id')
            ->leftJoin('ortu', 'ortu.santri_id', '=', 'santri.id')
            ->select(
                'santri.id as santri_id',
                'santri.nama as nama',
                'ortu.nama as nama_ortu',
                'ortu.no_hp as no_hp_ortu'
            )
            ->get();

        // 1. Ambil jadwal untuk hari ini saja
        $jadwalHariIni = JadwalKegiatan::whereDate('tanggal', now()->toDateString())
            ->orderBy('waktu_mulai')
            ->get();

        // 2. Jika tidak ada jadwal hari ini, atur rekap kosong
        if ($jadwalHariIni->isEmpty()) {
            $rekap = $santris->map(fn($santri) => (object) [
                'santri_id' => $santri->santri_id,
                'nama' => $santri->nama,
                'nama_ortu' => $santri->nama_ortu,
                'no_hp_ortu' => $santri->no_hp_ortu,
                'total_jadwal' => 0,
                'sudah_lapor' => 0,
                'belum_lapor' => 0,
                'tidak_lapor' => 0,
                'persentase' => 0,
            ]);
        } else {
            // 3. Proses data santri untuk hari ini
            $rekap = $santris->map(function ($santri) use ($jadwalHariIni) {
                $sudahLapor = 0;
                $belumLapor = 0;
                $tidakLapor = 0;

                foreach ($jadwalHariIni as $jadwal) {
                    $laporanExists = Laporan::where('santri_id', $santri->santri_id)
                        ->where('jadwal_id', $jadwal->id)
                        ->where(function ($query) {
                            $query->whereNotNull('bukti_laporan')
                                ->orWhereNotNull('keterangan');
                        })
                        ->exists();

                    // Cek apakah jadwal sudah selesai
                    $jadwalDateTimeEnd = Carbon::parse($jadwal->tanggal . ' ' . str_replace('.', ':', $jadwal->waktu_selesai));

                    if ($jadwalDateTimeEnd->lt(now())) {
                        if ($laporanExists) {
                            $sudahLapor++;
                        } else {
                            $tidakLapor++;
                        }
                    } else {
                        if ($laporanExists) {
                            $sudahLapor++;
                        } else {
                            $belumLapor++;
                        }
                    }
                }

                $totalJadwal = $jadwalHariIni->count();
                $jadwalSelesai = $sudahLapor + $tidakLapor;
                $persentase = $jadwalSelesai > 0 ? round(($sudahLapor / $jadwalSelesai) * 100, 2) : 0;

                return (object) [
                    'santri_id' => $santri->santri_id,
                    'nama' => $santri->nama,
                    'nama_ortu' => $santri->nama_ortu,
                    'no_hp_ortu' => $santri->no_hp_ortu,
                    'total_jadwal' => $totalJadwal,
                    'sudah_lapor' => $sudahLapor,
                    'belum_lapor' => $belumLapor,
                    'tidak_lapor' => $tidakLapor,
                    'persentase' => $persentase,
                ];
            });
        }

        $type = $request->query('type');

        // Filter data berdasarkan tipe yang diminta
        if ($type === 'uploaded') {
            $rekap = $rekap->filter(fn($data) => $data->sudah_lapor > 0);
        } elseif ($type === 'not_uploaded') {
            $rekap = $rekap->filter(fn($data) => $data->tidak_lapor > 0 || $data->belum_lapor > 0);
        }

        return view('admin.upload-tidakLaporan', compact('role', 'rekap', 'profile', 'type'));
    }
}
