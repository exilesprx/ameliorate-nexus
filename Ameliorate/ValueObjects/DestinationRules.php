<?php

namespace Ameliorate\ValueObjects;

use Closure;
use Exception;

/**
 * Class DestinationRules
 * @package Ameliorate\ValueObjects
 */
class DestinationRules
{
    /** @var bool */
    protected $catchesExceptions;

    /** @var Closure|null */
    protected $handler;

    public function __construct(bool $catchesExceptions = true, Closure $handler = null)
    {
        $this->catchesExceptions = $catchesExceptions;

        $this->handler = $handler;
    }

    /**
     * Determines if exceptions should be caught.
     *
     * @return bool
     */
    public function shouldCatchExceptions() : bool
    {
        return $this->catchesExceptions;
    }

    /**
     * Handles the exception via the handler if one is defined.
     *
     * @param Exception $exception
     * @param $traveler
     * @return bool
     * @throws Exception
     */
    public function handle(Exception  $exception, $traveler) : bool
    {
        if (!$this->handler instanceof Closure && !is_callable($this->handler)) {
            throw new Exception("Handler not defined");
        }

        return $this->handler->call($this->handler, $exception, $traveler);
    }
}