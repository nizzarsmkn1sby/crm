<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\LeadCreated;
use App\Events\LeadStatusChanged;
use App\Listeners\AutomationRunner;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register automation event listeners
        Event::listen(LeadCreated::class, [AutomationRunner::class, 'handleLeadCreated']);
        Event::listen(LeadStatusChanged::class, [AutomationRunner::class, 'handleLeadStatusChanged']);
    }
}
