<?php

namespace tests\Helpers;

use Closure;
use Illuminate\Support\Arr;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container implements \Illuminate\Contracts\Container\Container
{

    public function bound($abstract)
    {
        // TODO: Implement bound() method.
    }

    public function alias($abstract, $alias)
    {
        // TODO: Implement alias() method.
    }

    public function tag($abstracts, $tags)
    {
        // TODO: Implement tag() method.
    }

    public function tagged($tag)
    {
        // TODO: Implement tagged() method.
    }

    public function bind($abstract, $concrete = null, $shared = false)
    {
        // TODO: Implement bind() method.
    }

    public function bindIf($abstract, $concrete = null, $shared = false)
    {
        // TODO: Implement bindIf() method.
    }

    public function singleton($abstract, $concrete = null)
    {
        // TODO: Implement singleton() method.
    }

    public function extend($abstract, Closure $closure)
    {
        // TODO: Implement extend() method.
    }

    public function instance($abstract, $instance)
    {
        // TODO: Implement instance() method.
    }

    public function when($concrete)
    {
        // TODO: Implement when() method.
    }

    public function factory($abstract)
    {
        // TODO: Implement factory() method.
    }

    public function make($abstract, array $parameters = [])
    {
        // TODO: Implement make() method.
    }

    public function call($callback, array $parameters = [], $defaultMethod = null)
    {
        // TODO: Implement call() method.
    }

    public function resolved($abstract)
    {
        // TODO: Implement resolved() method.
    }

    public function resolving($abstract, Closure $callback = null)
    {
        // TODO: Implement resolving() method.
    }

    public function afterResolving($abstract, Closure $callback = null)
    {
        // TODO: Implement afterResolving() method.
    }

    public function get($id)
    {
        // TODO: Implement get() method.
    }

    public function has($id)
    {
        // TODO: Implement has() method.
    }
}