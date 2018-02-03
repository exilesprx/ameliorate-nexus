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
     * Set the stops of the nexus.
     *
     * @param  dynamic|array $stops
     * @return $this
     */
    public function to($stops);

    /**
     * Set the method to call on the stops.
     *
     * @param  string $method
     * @return $this
     */
    public function via($method);

    /**
     * Run the nexus with a final destination callback.
     *
     * @param  \Closure $destination
     * @return mixed
     */
    public function arrive(Closure $destination);
}