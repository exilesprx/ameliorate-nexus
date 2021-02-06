<?php

namespace spec\Ameliorate\Helpers;

use Ameliorate\Contracts\DestinationContract;
use Ameliorate\Contracts\TravelerContract;

class DestinationOne implements DestinationContract
{

    public function handle(TravelerContract $traveler, \Closure $next) : bool
    {
        // TODO: Implement handle() method.
    }
}