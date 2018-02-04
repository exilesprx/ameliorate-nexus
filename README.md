# Ameliorate Nexus

A package to setup a nexus of stops which are processed based on the result of the previous stop.

## Contract

Send a traveler (payload) through an array of jobs.
```
public function send($traveler);
```

Set the stops the traveler (payload) will travel to. Not all stops will be executed. See "stops" below.
```
public function to($stops);
```

Set the method the stops should execute.
```
public function via($method);
```

Set the final destination of the traveler (payload).
```
public function arrive(Closure $destination);
```

## Stops
Each stop should return either true or false. This return value will determine which stop is executed next.
```
$stops = [
//                     False                    True
    JobOne::class   => [JobTwo::class,          JobThree::class],
    JobThree::class => [JobFour::class,         JobFive::class],
    JobFour::class  => [JobFive::class,         JobSix::class],
    JobFive::class  => [JobSix::class,          Nexus::UNINHABITED],
    JobSix::class   => [Nexus::STOP,            Nexus::STOP] // JobSix always returns true
];
```

### Halt processing
Use the constant ```Nexus::STOP``` in order for the execution to jump to the final destination.

### No job to process
Use the constant ```Nexus::UNINHABITED``` to fill the job array in unreachable locations, but the array must be filled.

## Usage

```
$nexus = new Nexus($container);

$traveler = ["person" => ["age" => 27, "name" => "tester"]];

$stops = [
//                     False                    True
    JobOne::class   => [JobTwo::class,          JobThree::class],
    JobThree::class => [JobFour::class,         JobFive::class],
    JobFour::class  => [JobFive::class,         JobSix::class],
    JobFive::class  => [JobSix::class,          Nexus::UNINHABITED],
    JobSix::class   => [Nexus::STOP,            Nexus::STOP] // JobSix always returns true
];

$finalDestination = function($destination) {
    print_r($destination);
};

$nexus->send($traveler)
      ->to($stops)
      ->arrive($finalDestination);

// Output
//   Note: Each job append its short name and the value it returns.
//[
//  [person] => [
//      [age]   => 27,
//      [name]  => tester
//  ],
//  [jobone]    => true,
//  [jobthree]  => false,
//  [jobfive]   => false,
//  [jobsix]    => true
//]
```

#### Credits
Laravel's pipeline contract for a common API interface.