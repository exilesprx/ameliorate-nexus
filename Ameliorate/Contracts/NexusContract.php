<?php

namespace Ameliorate\Contracts;

use Closure;

/**
 * Interface NexusContract
 * @package Ameliorate\Contracts
 */
interface NexusContract
{
    /**
     * Set the traveler object being sent on the nexus.
     *
     * @param  TravelerContract $traveler
     * @return self
     */
    public function send(TravelerContract $traveler) : NexusContract;

    /**
     * Set the destinations of the nexus.
     *
     * @param  array $destinations
     * @return self
     */
    public function to(array $destinations) : NexusContract;

    /**
     * Set the method to call on the destinations.
     *
     * @param  string $method
     * @return self
     */
    public function via(string $method) : NexusContract;

    /**
     * Run the nexus with a final destination callback.
     *
     * @param  Closure $destination
     * @return void
     */
    public function arrive(Closure $destination) : void;
}