<?php

namespace spec\Ameliorate\Helpers;

use Ameliorate\Contracts\TravelerContract;

class FakeTraveler implements TravelerContract
{
    public function getName() { return null; }

    public function getPayload(): array
    {
        return [];
    }
}