<?php

namespace spec\Ameliorate;

use Ameliorate\Nexus;
use Ameliorate\ValueObjects\DestinationRules;
use Illuminate\Contracts\Container\Container;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RuntimeException;
use spec\Ameliorate\Helpers\DestinationOne;
use spec\Ameliorate\Helpers\DestinationTwo;
use spec\Ameliorate\Helpers\DestinationThree;
use spec\Ameliorate\Helpers\FakeTraveler;
use spec\Ameliorate\Helpers\FakeHandler;
use spec\Ameliorate\Helpers\FakeArrival;

/**
 * Class NexusSpec
 * @package spec\Ameliorate
 */
class NexusSpec extends ObjectBehavior
{
    /**
     * Nexus should be constructor with a container instance
     *
     * @param Container|\PhpSpec\Wrapper\Collaborator $container
     * @param DestinationRules $rules
     */
    public function let(Container $container, DestinationRules $rules)
    {
        $this->beConstructedWith($rules, $container);
    }

    /**
     * Self explanatory
     */
    public function it_is_initializable()
    {
        $this->shouldHaveType(Nexus::class);
    }

    /**
     * Make sure the same instance is return so chaining can occur
     */
    public function it_should_return_this_when_calling_send(FakeTraveler $traveler)
    {
        $this->send($traveler)->shouldReturn($this);
    }

    /**
     * Make sure the same instance is return so chaining can occur
     */
    public function it_should_return_this_when_calling_to()
    {
        $destinations = [];

        $this->to($destinations)->shouldReturn($this);
    }

    /**
     * The arrive function doesn't return, so make sure the return is null
     */
    public function it_should_return_null_when_calling_arrive()
    {
        $arrival = function() {};

        $this->to([Nexus::STOP]);

        $this->arrive($arrival)->shouldReturn(null);
    }

    /**
     * Make sure the initial class is called
     *
     * @param \PhpSpec\Wrapper\Collaborator|DestinationOne $one
     * @param \PhpSpec\Wrapper\Collaborator $container
     */
    public function it_should_call_handle_on_class_one_once(DestinationOne $one, FakeTraveler $traveler, $container)
    {
        $destinations = [
            DestinationOne::class => [Nexus::STOP, Nexus::STOP]
        ];

        $arrival = function() {};

        $container->make(DestinationOne::class)
            ->shouldBeCalled()
            ->willReturn($one);

        $one->handle(Argument::any(), Argument::type('Closure'))
            ->shouldBeCalled()
            ->willReturn(true);

        $this->send($traveler)
            ->to($destinations)
            ->arrive($arrival)
            ->shouldReturn(null);
    }

    /**
     * Make sure each class that is processed it called once
     *
     * @param \PhpSpec\Wrapper\Collaborator|DestinationOne $one
     * @param \PhpSpec\Wrapper\Collaborator|DestinationTwo $two
     * @param \PhpSpec\Wrapper\Collaborator|DestinationThree $three
     * @param \PhpSpec\Wrapper\Collaborator $container
     */
    public function it_should_call_handle_on_class_one_two_and_three_once(DestinationOne $one, DestinationTwo $two, DestinationThree $three, FakeTraveler $traveler, $container)
    {
        $destinations = [
            DestinationOne::class => [DestinationTwo::class, Nexus::UNINHABITED],
            DestinationTwo::class => [Nexus::UNINHABITED, DestinationThree::class],
            DestinationThree::class => [Nexus::STOP, Nexus::STOP]
        ];

        $arrival = function() {};

        $container->make(DestinationOne::class)
            ->shouldBeCalled()
            ->willReturn($one);

        $container->make(DestinationTwo::class)
            ->shouldBeCalled()
            ->willReturn($two);

        $container->make(DestinationThree::class)
            ->shouldBeCalled()
            ->willReturn($three);

        $one->handle(Argument::any(), Argument::type('Closure'))
            ->shouldBeCalled()
            ->willReturn(false);

        $two->handle(Argument::any(), Argument::type('Closure'))
            ->shouldBeCalled()
            ->willReturn(true);

        $three->handle(Argument::any(), Argument::type('Closure'))
            ->shouldBeCalled()
            ->willReturn(true);

        $this->send($traveler)
            ->to($destinations)
            ->arrive($arrival)
            ->shouldReturn(null);
    }

    /**
     * Make sure a class can be used as the traveler
     *
     * @param \PhpSpec\Wrapper\Collaborator|FakeTraveler $traveler
     * @param \PhpSpec\Wrapper\Collaborator $container
     */
    public function it_should_use_a_class_as_the_traveler(FakeTraveler $traveler, $container)
    {
        $destinations = [
            FakeHandler::class => [Nexus::STOP, Nexus::STOP]
        ];

        $arrival = function() {};

        $container->make(FakeHandler::class)
            ->shouldBeCalled()
            ->willReturn(new FakeHandler());

        $traveler->getName()
            ->shouldBeCalled()
            ->willReturn(false);

        $this->send($traveler)
            ->to($destinations)
            ->arrive($arrival)
            ->shouldReturn(null);
    }

    /**
     * Make sure the final destination is eventually called
     *
     * @param \PhpSpec\Wrapper\Collaborator|FakeArrival $arrive
     */
    public function it_should_call_the_final_destination(FakeArrival $arrive, FakeTraveler $traveler)
    {
        $destinations = [
            Nexus::STOP
        ];

        $arrive->handle()->shouldBeCalled();

        $this->send($traveler)
            ->to($destinations)
            ->arrive(function() use($arrive) {
                $arrive->getWrappedObject()->handle();
            });
    }

    /**
     * Make sure a run time exception is thrown if a destination
     * does not implement the handle method.
     *
     * @param \PhpSpec\Wrapper\Collaborator $container
     */
    public function it_should_throw_a_runtime_exception_when_resolving_a_class(FakeTraveler $traveler, $container)
    {
        $destinations = [
            "SomeFakeClass" => [Nexus::STOP, Nexus::STOP],
        ];

        $arrival = function() {};

        $container->make("SomeFakeClass")
            ->shouldBeCalled()
            ->willReturn("SomeFakeClass");

        $this->send($traveler)
            ->to($destinations);

        $this->shouldThrow(new RuntimeException("SomeFakeClass must implement a handle(mixed, Closure) function."))
            ->during('arrive', [$arrival]);
    }
}