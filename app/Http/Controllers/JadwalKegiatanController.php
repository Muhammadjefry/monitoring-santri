<?php

namespace App\Http\Controllers;

use App\Models\JadwalKegiatan;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class JadwalKegiatanController extends Controller
{
    public function index()
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
                'santri.id as santri_id'
            )
            ->first();


        $jadwals = JadwalKegiatan::orderBy('tanggal', 'DESC')
            ->orderBy('waktu_mulai', 'DESC')
            ->get();

        return view('admin.jadwal', compact('jadwals', 'profile'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'judul_kegiatan' => 'required|string',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i',
        ]);

        if (strtotime($request->tanggal) < strtotime(date('Y-m-d'))) {
            return redirect()->route('admin.jadwal')
                ->withInput()
                ->with('error', 'Tanggal tidak boleh kurang dari hari ini.');
        }

        if (strtotime($request->waktu_selesai) <= strtotime($request->waktu_mulai)) {
            return redirect()->route('admin.jadwal')
                ->withInput()
                ->with('error', 'Waktu selesai harus lebih dari waktu mulai.');
        }

        $jadwal = JadwalKegiatan::create($request->all());

        $targetUsers = User::whereIn('role', ['SANTRI', 'ORANG TUA'])->get();

        foreach ($targetUsers as $user) {
            Notifikasi::create([
                'user_id' => $user->id,
                'judul' => 'Jadwal Baru',
                'pesan' => 'Kegiatan "' . $jadwal->judul_kegiatan . '" telah dijadwalkan pada tanggal ' . $jadwal->tanggal,
            ]);
        }

        return redirect()->route('admin.jadwal')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $jadwal = JadwalKegiatan::findOrFail($id);
        return view('jadwal.edit', compact('jadwal'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'judul_kegiatan' => 'required|string',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
        ]);

        if (strtotime($request->waktu_selesai) <= strtotime($request->waktu_mulai)) {
            return redirect()->route('admin.jadwal')
                ->withInput()
                ->with('error', 'Waktu selesai harus lebih dari waktu mulai.');
        }

        $jadwal = JadwalKegiatan::findOrFail($id);
        $jadwal->update($request->all());


        return redirect()->route('admin.jadwal')->with('success', 'Jadwal berhasil diperbarui.');
    }
    public function destroy(Request $request)
    {
        $jadwal = JadwalKegiatan::find($request->id);

        if (!$jadwal) {
            return redirect()->route('admin.jadwal')->with('error', 'Jadwal tidak ditemukan.');
        }

        $jadwal->delete();

        return redirect()->route('admin.jadwal')->with('success', 'Jadwal berhasil dihapus.');
    }
}
