<?php

namespace Tests;

use Ameliorate\Contracts\DestinationContract;
use Ameliorate\Nexus;
use Illuminate\Contracts\Container\Container;
use PHPUnit\Framework\TestCase;
use Faker\Factory as Faker;

/**
 * Class NexusTest
 * @package Tests
 */
class NexusTest extends TestCase
{
    private $faker;

    /**
     * @test
     * @throws \ReflectionException
     */
    public function it_should_return_this_when_calling_send()
    {
        $nexus = $this->createMock(Nexus::class);

        $traveler = [
            $this->faker->words(3, true)
        ];

        $nexus->expects($this->once())
            ->method('send')
            ->with($traveler)
            ->willReturn($nexus);

        $nexus->send($traveler);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function it_should_return_this_when_calling_to()
    {
        $nexus = $this->createMock(Nexus::class);

        $stops = [
            $this->faker->word(),
            $this->faker->word()
        ];

        $nexus->expects($this->once())
            ->method('to')
            ->with($stops)
            ->willReturn($nexus);

        $nexus->to($stops);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function it_should_return_void_when_calling_arrive()
    {
        $nexus = $this->createMock(Nexus::class);

        $destination = function() {
            // Does nothing
        };

        $nexus->expects($this->once())
            ->method('arrive')
            ->with($destination)
            ->willReturn($nexus);

        $nexus->arrive($destination);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function it_should_run_a_callable()
    {
        $container = $this->createMock(Container::class);
        $firstClass = $this->createMock(FirstClass::class);

        $nexus = new Nexus($container);

        $container->expects($this->at(0))
            ->method('make')
            ->willReturn($firstClass);

        $firstClass->method('handle')
            ->willReturn(false);

        $traveler = [
            "name" => $this->faker->words(5, true)
        ];

        $callable = function($payload, \Closure $next) use ($traveler) {

            $this->assertNotNull($payload);
            $this->assertEquals($traveler, $payload);

            $payload["callable"] = true;

            return $next($payload, true);
        };

        $stops = [
            FirstClass::class  => [$callable, Nexus::STOP],
            Nexus::STOP
        ];

        $nexus->send($traveler)
            ->to($stops)
            ->arrive(
                function($payload) use($traveler) {
                    $this->assertEquals(
                        array_merge(
                            $traveler,
                            ['callable' => true]
                        ),
                        $payload
                    );
                }
            );
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    function it_should_run_first_class()
    {
        $container = $this->createMock(Container::class);

        $firstClass = $this->getMockBuilder(FirstClass::class)
            ->setMethods(['handle'])
            ->getMock();

        $nexus = new Nexus($container);

        $container->expects($this->at(0))
            ->method('make')
            ->willReturn($firstClass);

        $traveler = [
            "name" => $this->faker->words(5, true)
        ];

        $stops = [
            FirstClass::class  => [Nexus::STOP, Nexus::STOP],
        ];

        $firstClass->expects($this->once())
            ->method('handle')
            ->willReturn(false);

        $nexus->send($traveler)
            ->to($stops)
            ->arrive(
                function($payload) use($firstClass, $traveler) {
                    $this->assertEquals(
                        $traveler,
                        $payload
                    );
                }
            );
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    function it_should_process_all_classes_once()
    {
        $container = $this->createMock(Container::class);
        $firstClass = $this->createMock(DestinationContract::class);
        $secondClass = $this->createMock(SecondClass::class);
        $thirdClass = $this->createMock(ThirdClass::class);

        $nexus = new Nexus($container);

        $container->expects($this->at(0))
            ->method('make')
            ->willReturn($firstClass);

        $container->expects($this->at(1))
            ->method('make')
            ->willReturn($secondClass);

        $container->expects($this->at(2))
            ->method('make')
            ->willReturn($thirdClass);

        $traveler = [];

        $stops = [
            FirstClass::class  => [SecondClass::class, ThirdClass::class],
            SecondClass::class => [ThirdClass::class, ThirdClass::class],
            ThirdClass::class  => [Nexus::STOP, Nexus::STOP]
        ];

        $firstClass->expects($this->once())
            ->method('handle')
            ->willReturn(false);

        $secondClass->expects($this->once())
            ->method('handle')
            ->willReturn(false);

        $thirdClass->expects($this->once())
            ->method('handle')
            ->willReturn(false);

        $nexus->send($traveler)
            ->to($stops)
            ->arrive(
                function($payload) use($firstClass, $secondClass, $thirdClass) {
                    $this->assertEquals(0, count($payload));
                }
            );
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    function it_should_accept_an_class_as_traveler()
    {
        $container = $this->createMock(Container::class);
        $firstClass = $this->createMock(DestinationContract::class);

        $nexus = new Nexus($container);

        $container->expects($this->at(0))
            ->method('make')
            ->willReturn($firstClass);

        $traveler = new Traveler();

        $name = $this->faker->name();

        $callable = function(Traveler $payload, \Closure $next) use ($name) {

            $payload->setName($name);

            return $next($payload, true);
        };

        $stops = [
            FirstClass::class  => [$callable, Nexus::UNINHABITED],
            Nexus::STOP
        ];

        $firstClass->expects($this->once())
            ->method('handle')
            ->willReturn(false);

        $nexus->send($traveler)
            ->to($stops)
            ->arrive(
                function(Traveler $payload) use($name) {
                    $this->assertEquals(
                        $payload->getName(),
                        $name
                    );
                }
            );
    }

    /**
     * @test
     * @throws \ReflectionException
     * @expectedException \RuntimeException
     * @expectedExceptionMessage SomeFakeClass must implement a handle(mixed, Closure) function
     */
    function it_should_throw_a_runtime_exception_when_resolving_a_class()
    {
        $container = $this->createMock(Container::class);

        $nexus = new Nexus($container);

        $traveler = [];

        $stops = [
            "SomeFakeClass" => [Nexus::STOP, Nexus::STOP]
        ];

        $nexus->send($traveler)
            ->to($stops)
            ->arrive(
                function($payload) {
                    $this->assertNotNull($payload);
                }
            );
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    function it_should_call_final_destination()
    {
        $container = $this->createMock(Container::class);

        $nexus = new Nexus($container);

        $name = $this->faker->words(2, true);

        $traveler = [
            "name" => $name
        ];

        $stops = [Nexus::STOP];

        $nexus->send($traveler)
            ->to($stops)
            ->arrive(
                function($payload) use ($traveler) {
                    $this->assertEquals(
                        $traveler,
                        $payload
                    );
                }
            );
    }

    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        parent::setUp();

        $this->faker = Faker::create();
    }
}

/**
 * Some basic classes used for testing
 */

class FirstClass implements DestinationContract
{
    public function handle($luggage, \Closure $next)
    {
        $luggage[self::class] = true;

        return $next($luggage, false);
    }
}

class SecondClass implements DestinationContract
{
    public function handle($luggage, \Closure $next)
    {
        $luggage[self::class] = true;

        return $next($luggage, false);
    }
}

class ThirdClass implements DestinationContract
{
    public function handle($luggage, \Closure $next)
    {
        $luggage[self::class] = true;

        return $next($luggage, false);
    }
}

class Traveler
{
    private $name;

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }
}