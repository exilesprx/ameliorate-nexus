# Ameliorate Nexus

A package to setup a nexus of stops which are processed based on the result of the previous stop.

## Contract

Send a traveler (payload) through an array of jobs. The traveler can also be a class, such as a Domain Transfer Object (DTO), since the argument accepts mixed types.
```php
public function send($traveler);
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

## Stops
Each stop should return either true or false. This return value will determine which stop is executed next.
```php
$destinations = [
//                     False                    True
    JobOne::class   => [JobTwo::class,          JobThree::class],
    JobThree::class => [JobFour::class,         JobFive::class],
    JobFour::class  => [JobFive::class,         JobSix::class],
    JobFive::class  => [JobSix::class,          Nexus::UNINHABITED],
    JobSix::class   => [Nexus::STOP,            Nexus::STOP] // JobSix always returns true
];
```

### Halt processing
Use the constant `Nexus::STOP` in order for the execution to jump to the final destination.

### No job to process
Use the constant `Nexus::UNINHABITED` to fill the job array in unreachable locations, but the array must be filled.

## Usage

```php
$nexus = new Nexus($container);

$traveler = ["person" => ["age" => 27, "name" => "tester"]];

$destinations = [
//                     False                    True
    JobOne::class   => [JobTwo::class,          JobThree::class],
    JobThree::class => [JobFour::class,         JobFive::class],
    JobFour::class  => [JobFive::class,         JobSix::class],
    JobFive::class  => [JobSix::class,          Nexus::UNINHABITED],  // JobSix always returns true, so Nexus:UNINHABITED is used.
    JobSix::class   => [Nexus::STOP,            Nexus::STOP]          // End the processing by using Nexus::STOP
];

$finalDestination = function($destination) {
    print_r($destination);
};

$nexus->send($traveler)
      ->to($destinations)
      ->arrive($finalDestination);

// Output
//   Note: For this example, each job appends its short name and the value it returns.
//  [
//      [person] => [
//          [age]   => 27,
//          [name]  => tester
//      ],
//      [jobone]    => true,
//      [jobthree]  => false,
//      [jobfive]   => false,
//      [jobsix]    => true
//  ]
```

#### Credits
Laravel's pipeline contract for a common API interface.