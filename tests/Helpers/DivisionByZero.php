<?php

namespace tests\Helpers;

use Ameliorate\Contracts\DestinationContract;
use Ameliorate\Contracts\TravelerContract;
use Closure;

class DivisionByZero implements DestinationContract
{
    public static $MESSAGE = "Cannot divide by zero";

    public function handle(TravelerContract $traveler, Closure $next) : bool
    {
        if (!$traveler instanceof Math) {
            return $next($traveler, false);
        }

        throw new \Exception(self::$MESSAGE);

        return $next($traveler, false); // Shouldn't reach this point
    }
}