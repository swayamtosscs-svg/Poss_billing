<?php

namespace App\Providers;

use App\Events\SaleRecorded;
use App\Listeners\UpdateStockAndLoyalty;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        SaleRecorded::class => [
            UpdateStockAndLoyalty::class,
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
