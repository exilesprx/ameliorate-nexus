<?php

namespace Ameliorate\Contracts;

/**
 * Interface DestinationContract
 * @package Ameliorate\Contracts
 */
interface DestinationContract
{
    /**
     * @param mixed $luggage
     * @param \Closure $next
     * @return mixed
     */
    public function handle($luggage, \Closure $next);
}