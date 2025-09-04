<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function showSantri()
    {
        $users = User::where('role', 'santri')->get();
        return view('admin.santri', compact('users'));
    }
    public function showOrtu()
    {
        return view('admin.ortu');
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'nama' => 'required',
            'password' => 'required',
            'role' => 'required|in:santri,ortu',
        ]);

        $existing = User::where('username', $request->username)
            ->orWhere('nama', $request->nama)
            ->first();

        if ($existing) {
            return redirect()->route('admin.santri')->with('error', 'Username atau nama sudah digunakan, silakan coba yang lain.');
        }


        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'nama' => $request->nama,
            'role' => $request->role,
            'kamar' => $request->kamar,
        ]);

        return redirect()->route('admin.santri')->with('success', 'User berhasil ditambahkan');
    }


    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required',
            'nama' => 'required',
        ]);

        $existing = User::where(function ($query) use ($request) {
            $query->where('username', $request->username)
                ->orWhere('nama', $request->nama);
        })
            ->where('id', '!=', $user->id)
            ->first();

        if ($existing) {
            return redirect()->route('admin.santri')
                ->with('error', 'Username atau nama sudah digunakan, silakan coba yang lain.');
        }

        $user->update([
            'username' => $request->username,
            'nama' => $request->nama,
            'kamar' => $request->kamar,
        ]);

        return redirect()->route('admin.santri')->with('success', 'User berhasil diupdate');
    }


    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.santri')->with('success', 'User berhasil dihapus');
    }

    public function resetPassword(User $user)
    {
        $user->password = bcrypt('123');
        $user->save();

        return redirect()->route('admin.santri')->with('success', 'Password berhasil di-reset menjadi 123.');
    }
}
