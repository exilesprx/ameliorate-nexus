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
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the nexus implementation on the container
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(NexusContract::class, Nexus::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Nexus::class];
    }
}