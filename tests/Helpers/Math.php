<?php

namespace tests\Helpers;

use Ameliorate\Contracts\TravelerContract;

class Math implements TravelerContract
{

    private $value;

    public function  __construct(int $value)
    {
        $this->value = $value;
    }

    public function getValue() : float
    {
        return $this->value;
    }

    public function updateValue(float $value) : void
    {
        $this->value = $value;
    }

    public function getPayload(): array
    {
        return [
            "value" => $this->value
        ];
    }
}