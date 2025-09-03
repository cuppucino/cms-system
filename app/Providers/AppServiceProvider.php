<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Invitation;
use App\Models\GownCollection;
use App\Models\Payment;
use App\Models\SessionRegistration;
use App\Policies\UserPolicy;
use App\Policies\InvitationPolicy;
use App\Policies\GownCollectionPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\SessionRegistrationPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class                => UserPolicy::class,
        Invitation::class          => InvitationPolicy::class,
        GownCollection::class      => GownCollectionPolicy::class,
        Payment::class             => PaymentPolicy::class,
        SessionRegistration::class => SessionRegistrationPolicy::class,
    ];

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
        // Simple admin gate usable via middleware('can:admin')
        Gate::define('admin', fn(User $u) => (bool) $u->is_admin);

        // Fix default string length for older MySQL
        Schema::defaultStringLength(191);

        // Force InnoDB engine
        DB::statement('SET default_storage_engine=InnoDB');
    }
}
