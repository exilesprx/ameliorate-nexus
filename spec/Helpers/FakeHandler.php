<?php

namespace spec\Helpers;

class FakeHandler
{
    public function handle(FakeTraveler $traveler, \Closure $next)
    {
        return $next($traveler->getName(), false);
    }
}