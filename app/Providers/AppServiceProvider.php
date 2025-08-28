<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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
        Schema::defaultStringLength(191);
        DB::statement('SET default_storage_engine=InnoDB');

        // Share unread count globally (shows in StudentLayout bell)
        Inertia::share('unreadCount', function () {
            if (!Auth::check()) {
                return 0;
            }

            // If you use a custom notifications table/model:
            return \App\Models\Notification::where('user_id', Auth::id())
                ->where('is_read', false)   // or ->where('is_read', false)
                ->count();
        });

    }
}
