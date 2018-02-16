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
     * @param  mixed $traveler
     * @return $this
     */
    public function send($traveler);

    /**
     * Set the destinations of the nexus.
     *
     * @param  array $destinations
     * @return $this
     */
    public function to(array $destinations);

    /**
     * Set the method to call on the destinations.
     *
     * @param  string $method
     * @return $this
     */
    public function via(string $method);

    /**
     * Run the nexus with a final destination callback.
     *
     * @param  \Closure $destination
     * @return mixed
     */
    public function arrive(Closure $destination);
}