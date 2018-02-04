<?php

namespace Ameliorate\Providers;

use Ameliorate\Contracts\NexusContract;
use Ameliorate\Nexus;
use Illuminate\Support\ServiceProvider;

/**
 * Class NexusServiceProvider
 * @package Ameliorate\Providers
 */
class NexusServiceProvider extends ServiceProvider
{
    /**
     * Register the nexus implementation on the container
     */
    public function register()
    {
        $this->app->bind(NexusContract::class, Nexus::class);
    }
}