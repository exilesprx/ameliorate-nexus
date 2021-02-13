<?php

namespace Ameliorate;

use Ameliorate\Contracts\DestinationContract;
use Ameliorate\Contracts\TravelerContract;
use Ameliorate\ValueObjects\DestinationRules;
use Closure;
use Exception;
use RuntimeException;
use Ameliorate\Contracts\NexusContract;
use Illuminate\Contracts\Container\Container;

/**
 * Class Nexus
 * @package Ameliorate
 */
class Nexus implements NexusContract
{
    /**
     * Use this constant to arrive a final destination.
     */
    const STOP = "stop";

    /**
     * Use this constant to fill the array indexes which will never run.
     */
    const UNINHABITED = "nothing";

    /**
     * The container implementation.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The object being passed through the nexus.
     *
     * @var mixed
     */
    protected $traveler;

    /**
     * The array of class destinations.
     *
     * @var array
     */
    protected $destinations = [];

    /**
     * The method to call on each location.
     *
     * @var string
     */
    protected $method = 'handle';

    /**
     * The standard destination rules.
     *
     * @var DestinationRules
     */
    protected $rules;

    /**
     * Create a new class instance.
     *
     * @param DestinationRules $rules
     * @param \Illuminate\Contracts\Container\Container $container
     */
    public function __construct(DestinationRules $rules, Container $container)
    {
        $this->rules = $rules;

        $this->container = $container;
    }

    /**
     * Set the traveler object being sent on the nexus.
     *
     * @param  TravelerContract $traveler
     * @return self
     */
    public function send(TravelerContract $traveler) : NexusContract
    {
        $this->traveler = $traveler;

        return $this;
    }

    /**
     * Set the stops of the nexus. If a callable is
     * used, the process proceeds to the next index.
     *
     * @param  array $destinations
     * @return self
     */
    public function to(array $destinations) : NexusContract
    {
        $this->destinations = $destinations;

        return $this;
    }

    /**
     * Set the method to call on the destinations.
     *
     * @param  string $method
     * @return self
     */
    public function via(string $method) : NexusContract
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Run the nexus with a final destination callback.
     *
     * @param Closure $destination
     * @return void
     * @throws Exception
     */
    public function arrive(Closure $destination) : void
    {
        reset($this->destinations);

        $next = key($this->destinations);

        do {
            if($next == self::STOP) {
                break;
            }

            list($left, $right) = $this->destinations[$next];

            $obj = $this->resolve($next);

            if($goRight = $this->isTruthy($obj)) {
                $next = $right;

                continue;
            }

            $next = $left;
        }
        while($next);

        $destination($this->traveler);
    }

    /**
     * Closure that carries over into every location.
     *
     * @return Closure
     */
    protected function luggage() : Closure
    {
        return function($traveler, bool $bool) {

            $this->traveler = $traveler;

            return $bool;
        };
    }

    /**
     * Resolves objects using the container.
     *
     * @param string|DestinationContract $instance
     * @return DestinationContract
     */
    protected function resolve($instance) : DestinationContract
    {
        if (!$instance instanceof DestinationContract) {
            $instance = $this->container->make($instance);
        }

        if(!method_exists($instance, $this->method)) {
            throw new RuntimeException("{$instance} must implement a {$this->method}(mixed, Closure) function.");
        }

        return $instance;
    }

    /**
     * Determines if the the traveler should go right.
     *
     * @param DestinationContract $destination
     * @return bool
     * @throws Exception
     */
    protected function isTruthy(DestinationContract $destination) : bool
    {
        try {
            return $destination->handle($this->traveler, $this->luggage());
        } catch (Exception $exception) {

            if (!$this->rules->shouldCatchExceptions()) {
                throw $exception;
            }

            return $this->rules->handle($exception, $this->traveler);
        }
    }
}