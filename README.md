[![CircleCI](https://circleci.com/gh/exilesprx/ameliorate-nexus/tree/master.svg?style=svg)](https://circleci.com/gh/exilesprx/ameliorate-nexus/tree/master)

# Ameliorate Nexus

A package to setup a nexus of stops which are processed based on the result of the previous stop.

Note: Passing an object that is already initialized is not supported. That type of functionality should be handled by the container.

## Dependencies

A container to resolve class instance such as: ```https://github.com/illuminate/container```

## Nexus Contract

Send a traveler (payload) through an array of jobs.
```php
public function send(TravelerContract $traveler);
```

Set the destinations the traveler (payload) will travel to. Not all destinations will be executed. See "destinations" below.
```php
public function to(array $destinations);
```

Set the method the destinations should execute.
```php
public function via(string $method);
```

Set the final destination of the traveler (payload).
```php
public function arrive(Closure $destination);
```

## Destination Contract

Handle the traveler at the destination.
```php
public function handle(TravelerContract $traveler, Closure $next);
```

## Destinations
Each destination should return either true or false. This return value will determine which destination is executed next.
```php
$destinations = [
    Addition::class         => [Nexus::UNINHABITED, Multiplication::class],
    Multiplication::class   => [Subtraction::class, Nexus::UNINHABITED],
    Subtraction::class      => [Nexus::UNINHABITED, Division::class],
    Division::class         => [Nexus::STOP,        Nexus::STOP]
];
```

### Halt processing
Use the constant `Nexus::STOP` in order for the execution to jump to the final destination.

### No job to process
Use the constant `Nexus::UNINHABITED` to fill the job array in unreachable locations, but the array must be filled.

## Usage

```php
$rules = new DestinationRules(false);

$container = new Container();

$nexus = new Nexus($rules, $container);

$traveler = new Math(5); // starts with initial value of 5

/**
 * Destination path is as follows:
 * - start with 5
 * - add 3, then take truthy path
 * - multiply by 6, then take falsy path
 * - subtract 3, then take truthy path
 * - divide 4, then stop
 */
$destinations = [

    // Class to process        // False             // True

    // Container returns new Addition(3) - adds 3
    Addition::class         => [Nexus::UNINHABITED, Multiplication::class],
    
    // Container returns new Multiplication(6, false) - multiplies by 6
    Multiplication::class   => [Subtraction::class, Nexus::UNINHABITED],
    
    // Container returns new Subtraction(2) - subtracts 2
    Subtraction::class      => [Nexus::UNINHABITED, Division::class],
    
    // Container returns new Division(4) - divides by 4
    Division::class         => [Nexus::STOP,        Nexus::STOP]
];

$nexus->send($traveler)
    ->to($destinations)
    ->arrive(function(Math $traveler) {
        Assert::assertEquals(11.5, $traveler->getValue());
    });
```

#### Credits
Laravel's pipeline contract for a common API interface.