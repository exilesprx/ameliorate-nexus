<?php

namespace spec\Ameliorate\Helpers;

use Ameliorate\Contracts\DestinationContract;
use Ameliorate\Contracts\TravelerContract;

class FakeHandler implements DestinationContract
{
    public function handle(TravelerContract $traveler, \Closure $next) : bool
    {
        return $next($traveler->getName(), false);
    }
}