<?php

namespace Ameliorate;

use Ameliorate\Contracts\DestinationContract;
use Closure;
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
     * Create a new class instance.
     *
     * @param  \Illuminate\Contracts\Container\Container|null $container
     * @return void
     */
    public function __construct(Container $container = null)
    {
        $this->container = $container;
    }

    /**
     * Set the traveler object being sent on the nexus.
     *
     * @param  mixed $traveler
     * @return $this
     */
    public function send($traveler)
    {
        $this->traveler = $traveler;

        return $this;
    }

    /**
     * Set the stops of the nexus. If a callable is
     * used, the process proceeds to the next index.
     *
     * @param  array $destinations
     * @return $this
     */
    public function to(array $destinations)
    {
        $this->destinations = $destinations;

        return $this;
    }

    /**
     * Set the method to call on the destinations.
     *
     * @param  string $method
     * @return $this
     */
    public function via(string $method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Run the nexus with a final destination callback.
     *
     * @param  \Closure $destination
     * @return mixed
     */
    public function arrive(Closure $destination)
    {
        reset($this->destinations);

        $next = key($this->destinations);

        do {
            if($next == self::STOP) {
                break;
            }

            list($left, $right) = $this->destinations[$next];

            $obj = $this->resolve($next);

            if($goRight = $obj->handle($this->traveler, $this->luggage())) {
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
    protected function luggage()
    {
        return function($traveler, bool $bool) {

            $this->traveler = $traveler;

            return $bool;
        };
    }

    /**
     * Resolves objects using the container.
     *
     * @param string $instance
     * @return DestinationContract
     */
    protected function resolve(string $instance)
    {
        $instance = $this->container->make($instance);

        if(!method_exists($instance, $this->method)) {
            throw new RuntimeException("{$instance} must implement a {$this->method}(mixed, Closure) function.");
        }

        return $instance;
    }
}