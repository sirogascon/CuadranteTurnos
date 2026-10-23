<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\RosterStore;
use App\Stores\SessionRosterStore;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RosterStore::class, SessionRosterStore::class);
    }

    public function boot(): void
    {
        //
    }
}
