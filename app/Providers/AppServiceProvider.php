<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notifikasi;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share navbar data (user + notifications) to all views so components
        // like `x-navbar-auth` can safely consume them even when a controller
        // doesn't explicitly pass `$navbarData`.
        View::composer('*', function ($view) {
            $user = Auth::user();

            $userData = [];
            $notifications = [];

            if ($user) {
                $userData = [
                    'id' => $user->id,
                    'name' => $user->name ?? '',
                    'email' => $user->email ?? '',
                    'avatar' => $user->avatar ?? null,
                    'role' => $user->role ?? null,
                    'isStoreMember' => (($user->role ?? null) === 'penjual'),
                ];

                // Try to load latest notifications for the user (optional)
                try {
                    $notifications = Notifikasi::where('user_id', $user->id)
                        ->orderByDesc('created_at')
                        ->limit(10)
                        ->get()
                        ->map(function ($n) {
                            return [
                                'id' => $n->id,
                                'title' => $n->judul ?? '',
                                'message' => $n->pesan ?? '',
                                'time' => optional($n->created_at)->diffForHumans(),
                                'isRead' => ($n->status ?? '') === 'dibaca',
                                'icon' => null,
                            ];
                        })->toArray();
                } catch (\Exception $e) {
                    // If notifications table doesn't exist or query fails, ignore silently.
                    $notifications = [];
                }
            }

            $view->with('navbarData', [
                'userData' => $userData,
                'notifications' => $notifications,
            ]);
        });
    }
}
