# Interactor

[![Build Status](https://travis-ci.org/deefour/interactor.svg)](https://travis-ci.org/deefour/interactor)
[![Packagist Version](http://img.shields.io/packagist/v/deefour/interactor.svg)](https://packagist.org/packages/deefour/interactor)
[![Code Climate](https://codeclimate.com/github/deefour/interactor/badges/gpa.svg)](https://codeclimate.com/github/deefour/interactor)

Simple PHP Service Objects. Inspired by [collectiveidea/**interactor**](https://github.com/collectiveidea/interactor).

## Getting Started

Add Interactor to your `composer.json` file and run `composer update`. See [Packagist](https://packagist.org/packages/deefour/Interactor) for specific versions.

```
"deefour/interactor": "~0.4"
```

**`>=PHP5.5.0` is required.**

> **Note:** A work-in-progress attempt to explain how I use this package along with [`deefour/transformer`](https://github.com/deefour/transformer) and [`deefour/authorizer`](https://github.com/deefour/authorizer) to aide me in application development **[is available at this gist](https://gist.github.com/deefour/c6cfcebe808216a874f5)**.

## What is an Interactor

An interactor is a simple, single-purpose object.

Interactors are used to encapsulate your application's [business logic](http://en.wikipedia.org/wiki/Business_logic). Each interactor represents one thing that your application does.

An interactor must

 1. Extend `\Deefour\Interactor\Interactor`
 2. Implement a `call()` method.

### An Example

The below interactor creates a new `Car`.

```php
use Deefour\Interactor\Interactor;

class CreateCar extends Interactor {

  public function call() {
    $c = $this->context();

    $c->car = new Car([ 'make' => $c->make, 'model' => $c->model ]);

    if ( ! $c->car->save()) $this->fail();

    return $this;
  }

}
```

## Context

An interactor runs based on a given context. The context contains everything the interactor needs to do its work.

When an interactor calls its single purpose, it may affect the passed context.

The context can be an associative array or an implementation of `\Deefour\Interactor\Context`.

### Adding to the Context

As an interactor runs it can add information to the context.

```php
$this->context()->car = new Car;
```

### Requiring a Specific Context

The default implementation of the interactor's constructor allows accepts any valid context.

```php
new CreateCar([ 'make' => 'Honda', 'model' => 'Accord' ]);
```

This constructor can be overriden with a type-hinted context parameter to require a specific type of context be passed.

```php
public function __construct(CarContext $context = null) {
  return parent::__construct($context);
}
```

An implemenation of `CarContext` could then **require** a `$make` and `$model` be set on the context.

```php
use Deefour\Interactor\Context;

class CarContext extends Context {

  public $make;

  public $model;

  public function __construct($make, $model) {
    $this->make  = $make;
    $this->model = $model;
  }

}
```

The property assignments in the constructor could alternatively be delegated back to the `Deefour\Interactor\Context` superclass via [`get_defined_vars()`](http://php.net/manual/en/function.get-defined-vars.php). This is useful for contexts with many arguments in the constructor signature.

```
public function __construct($make, $model) {
  parent::__construct(get_defined_vars());
}
```

This will pass an array like `[ 'make' => 'Honda', 'model' => 'Accord' ]` up to the base context where assignment to the public properties will be called.

> It's good practice to explicitly define public properties for the arguments you want exposed to your interactor.

## Status

A context carries a status object. By default there is a `Success` status and an `Error` status. Contexts are given a successful status to start.

### Failing the Context

When something goes wrong in your interactor, you can flag the process as failed on the context.

```php
$this->context()->fail();

// or

$this->context()->fail('Some explicit error message here');
```

This swaps out the `Success` status for a new `Error` status. You can ask if the state is currently successful/passing.

```php
$c = $this->context();

$c->ok(); // true

$c->fail();

$c->ok(); // false

echo get_class($c->status()); //=> 'Deefour\Interactor\Status\Error'
```

## Usage

Within a controller, implementing the car creation through the `CreateCar` interactor might look like this.

```php
public function create(CreateRequest $request) {
  $context = new CarContext($request->get('make'), $request->get('model'));

  (new CreateCar($context))->call();

  if ($context->ok()) {
    echo 'Wow! Nice new ' . $context->car->make;
  } else {
    echo 'ERROR: ' . $context->status()->error();
  }
}
```

### Integration With Laravel 5

Within Laravel 5 a command can be treated as in interactor. The constructor and `handle()` methods both have type-hinted dependencies injected by the [IoC container](http://laravel.com/docs/master/container). An implementation of the `CreateCar` interactor as a command in Laravel 5 might look as follows:

```php
namespace App\Commands;

use App\Car;
use App\Contexts\CreateCarContext as CarContext;
use Illuminate\Contracts\Bus\SelfHandling;
use Deefour\Interactor\Interactor;
use Illuminate\Contracts\Redis\Database as Redis;

class CreateCar extends Interactor implements SelfHandling {

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct(CarContext $context) {
    parent::__construct($context);
  }

  /**
   * Execute the command.
   *
   * @return void
   */
  public function handle(Redis $redis) {
    $c->car = Car::create([ 'make' => $c->make, 'model' => $c->model ]);

    $redis->publish('A new' . (string)$car . ' was just added to the lot!');

    return $this->context();
  }

}
```

Include the `Deefour\Interactor\DispatchesInteractors` trait in your controller to use the `dispatchInteractor()` method.

```php
namespace App\Http\Controllers;

use App\Commands\CreateCar;
use App\Context\Car as CarContext;
use Deefour\Interactor\DispatchesInteractors;
use Illuminate\Http\Request;

class CarController extends BaseController {

  use DispatchesInteractors;

  public function create(Request $request) {
    $context = new CarContext($request->get('make'), $request->get('model'));

    $this->dispatchInteractor(CreateCar::class, $context);

    if ($context->ok()) {
      return 'Wow! Nice new ' . $context->car->make;
    } else {
      return 'ERROR: ' . $interactor->status()->error();
    }
  }

}
```

## Contribute

- Issue Tracker: https://github.com/deefour/interactor/issues
- Source Code: https://github.com/deefour/interactor

## Changelog

#### 0.4.4 - February 20, 2015

 - Added `permit()` method to provide a watered down version of [rails/strong_parameters](https://github.com/rails/strong_parameters) whitelisting against the contents of a `Context`.

#### 0.4.0 - February 1, 2015

 - Move much of the API out of the interactor and into the context
 - Changed `perform()` to `call()`
 - Add new trait with `dispatchInteractor()` method.

#### 0.3.0 - January 3, 2015

 - Refactor, striping out dependency on and support for Illuminate components.
 - Compatibility changes to work easily with Laravel 5's new command bus and event handlers.
 - Inverting resolution lookup; contexts now resolve interactors instead of the other way around.

#### 0.2.0 - October 7, 2014

 - Automatic context resolution from instantiated interactor.

#### 0.1.0 - October 2, 2014

 - Initial release

## License

Copyright (c) 2014 [Jason Daly](http://www.deefour.me) ([deefour](https://github.com/deefour)). Released under the [MIT License](http://deefour.mit-license.org/).








