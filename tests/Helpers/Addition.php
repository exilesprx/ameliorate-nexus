<?php

namespace tests\Helpers;

use Ameliorate\Contracts\DestinationContract;
use Ameliorate\Contracts\TravelerContract;
use Closure;

class Addition implements DestinationContract
{
    protected $value;

    protected $path;

    public function __construct(int $value, bool $path = true)
    {
        $this->value = $value;

        $this->path = $path;
    }

    public function handle(TravelerContract $traveler, Closure $next) : bool
    {
        if (!$traveler instanceof Math) {
            return $this->path;
        }

        $traveler->updateValue(
            $traveler->getValue() + $this->value
        );

        return $next($traveler, $this->path);
    }
}