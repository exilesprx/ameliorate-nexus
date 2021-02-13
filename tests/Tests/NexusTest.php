<?php

namespace tests;

use Ameliorate\Nexus;
use Ameliorate\ValueObjects\DestinationRules;
use Mockery;
use tests\Helpers\Addition;
use tests\Helpers\Container;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use tests\Helpers\Division;
use tests\Helpers\DivisionByZero;
use tests\Helpers\Math;
use tests\Helpers\Multiplication;
use tests\Helpers\Subtraction;

class NexusTest extends TestCase
{
    private $nexus;

    private $container;

    protected function setUp()
    {
        parent::setUp();

        $rules = new DestinationRules(false);

        $this->container = Mockery::mock(Container::class)->makePartial();

        $this->nexus = new Nexus($rules, $this->container);
    }

    public function testExpectedValueShouldEqualTen()
    {
        $math = new Math(5);
        $operation = new Addition(5);

        $this->container->shouldReceive('make')
            ->with(Addition::class)
            ->andReturn($operation);

        $destinations = [
            Addition::class => [Nexus::STOP, Nexus::STOP]
        ];

        $this->nexus->send($math)
            ->to($destinations)
            ->arrive(function(Math $traveler) {
                Assert::assertEquals(10, $traveler->getValue());
            });
    }

    public function testExpectedValueShouldEqualThree()
    {
        $math = new Math(5);
        $operation = new Subtraction(2);

        $this->container->shouldReceive('make')
            ->with(Subtraction::class)
            ->andReturn($operation);

        $destinations = [
            Subtraction::class => [Nexus::STOP, Nexus::STOP]
        ];

        $this->nexus->send($math)
            ->to($destinations)
            ->arrive(function(Math $traveler) {
                Assert::assertEquals(3, $traveler->getValue());
            });
    }

    public function testExpectedValueShouldEqualTwentyFive()
    {
        $math = new Math(5);
        $operation = new Multiplication(5);

        $this->container->shouldReceive('make')
            ->with(Multiplication::class)
            ->andReturn($operation);

        $destinations = [
            Multiplication::class => [Nexus::STOP, Nexus::STOP]
        ];

        $this->nexus->send($math)
            ->to($destinations)
            ->arrive(function(Math $traveler) {
                Assert::assertEquals(25, $traveler->getValue());
            });
    }

    public function testExpectedValueShouldEqualTwoPointFive()
    {
        $math = new Math(5);
        $operation = new Division(2);

        $this->container->shouldReceive('make')
            ->with(Division::class)
            ->andReturn($operation);

        $destinations = [
            Division::class => [Nexus::STOP, Nexus::STOP]
        ];

        $this->nexus->send($math)
            ->to($destinations)
            ->arrive(function(Math $traveler) {
                Assert::assertEquals(2.5, $traveler->getValue());
            });
    }

    public function testExpectedValueShouldThrowException()
    {
        $math = new Math(5);
        $operation = new DivisionByZero();

        $this->container->shouldReceive('make')
            ->with(DivisionByZero::class)
            ->andReturn($operation);

        $destinations = [
            DivisionByZero::class => [Nexus::STOP, Nexus::STOP]
        ];

        $this->expectExceptionMessage(DivisionByZero::$MESSAGE);
        $this->expectException(\Exception::class);

        $this->nexus->send($math)
            ->to($destinations)
            ->arrive(function(Math $traveler) {
                // Empty
            });
    }

    public function testExpectedValueShouldEqualElevenPointFive()
    {
        $math = new Math(5);
        $addition = new Addition(3);
        $subtraction = new Subtraction(2);
        $division = new Division(4);
        $multiplication = new Multiplication(6, false);

        $this->container->shouldReceive('make')
            ->with(Addition::class)
            ->andReturn($addition);

        $this->container->shouldReceive('make')
            ->with(Subtraction::class)
            ->andReturn($subtraction);

        $this->container->shouldReceive('make')
            ->with(Division::class)
            ->andReturn($division);

        $this->container->shouldReceive('make')
            ->with(Multiplication::class)
            ->andReturn($multiplication);

        $destinations = [
            Addition::class         => [Nexus::UNINHABITED, Multiplication::class],
            Multiplication::class   => [Subtraction::class, Nexus::UNINHABITED],
            Subtraction::class      => [Nexus::UNINHABITED, Division::class],
            Division::class         => [Nexus::STOP,        Nexus::STOP]
        ];

        $this->nexus->send($math)
            ->to($destinations)
            ->arrive(function(Math $traveler) {
                Assert::assertEquals(11.5, $traveler->getValue());
            });
    }

}