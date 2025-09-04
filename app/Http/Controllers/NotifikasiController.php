<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class NotifikasiController extends Controller
{
    public function markAllAsRead(Request $request)
    {
        $userId = Session::get('user_id');
        if ($userId) {
            Notifikasi::where('user_id', $userId)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }


        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }
}
