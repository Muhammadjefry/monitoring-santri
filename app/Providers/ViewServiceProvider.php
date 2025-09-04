<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Session;
use App\Models\Notifikasi;
use App\Models\Admin;
use App\Models\Santri;
use App\Models\Ortu;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function ($view) {
            $notifications = collect();
            $authUser = null;
            $userId = Session::get('user_id');
            $role = Session::get('role');

            if ($userId && $role) {
                // Ambil user detail sesuai role
                if ($role === 'ADMINISTRATOR') {
                    $authUser = Admin::where('user_id', $userId)->first();
                } elseif ($role === 'SANTRI') {
                    $authUser = Santri::where('user_id', $userId)->first();
                    $notifications = Notifikasi::where('user_id', $userId)->latest()->take(3)->get();
                } elseif ($role === 'ORANG TUA') {
                    $authUser = Ortu::where('user_id', $userId)->first();
                    $notifications = Notifikasi::where('user_id', $userId)->latest()->take(3)->get();
                }
            }

            $view->with([
                'authUser' => $authUser,
                'notifications' => $notifications
            ]);
        });
    }
}
