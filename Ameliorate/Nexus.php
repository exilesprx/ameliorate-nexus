<?php

namespace Ameliorate;

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
     * The array of class stops.
     *
     * @var array
     */
    protected $stops = [];

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
     * Set the stops of the nexus.
     *
     * @param  dynamic|array $stops
     * @return $this
     */
    public function to($stops)
    {
        $this->stops = is_array($stops) ? $stops : func_get_args();

        return $this;
    }

    /**
     * Set the method to call on the locations.
     *
     * @param  string $method
     * @return $this
     */
    public function via($method)
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
        reset($this->stops);

        $next = key($this->stops);

        do {
            if($next == self::STOP) {
                break;
            }

            $obj = $this->resolve($next);

            $turnRight = $obj->handle($this->traveler, $this->luggage());

            list($left, $right) = $this->stops[$next];

            if($turnRight) {
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
     * @param $instance
     * @return mixed
     */
    protected function resolve($instance)
    {
        $object = $this->container->make($instance);

        if(!method_exists($object, $this->method)) {
            throw new RuntimeException("{$object} must implement a {$this->method}(bool, Closure) function.");
        }

        return $object;
    }
}