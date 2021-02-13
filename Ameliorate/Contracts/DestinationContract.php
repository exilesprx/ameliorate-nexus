<?php

namespace Ameliorate\Contracts;

use Closure;

/**
 * Interface DestinationContract
 * @package Ameliorate\Contracts
 */
interface DestinationContract
{
    /**
     * @param TravelerContract $traveler
     * @param Closure $next
     * @return bool
     */
    public function handle(TravelerContract $traveler, Closure $next) : bool;
}