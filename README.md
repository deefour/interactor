# Interactor

[![Build Status](https://travis-ci.org/deefour/interactor.svg)](https://travis-ci.org/deefour/interactor)
[![Packagist Version](http://img.shields.io/packagist/v/deefour/interactor.svg)](https://packagist.org/packages/deefour/interactor)
[![Code Climate](https://codeclimate.com/github/deefour/interactor/badges/gpa.svg)](https://codeclimate.com/github/deefour/interactor)

Simple PHP Service Objects. Inspired by [collectiveidea/**interactor**](https://github.com/collectiveidea/interactor).

## Getting Started

Add Interactor to your `composer.json` file and run `composer update`. See [Packagist](https://packagist.org/packages/deefour/Interactor) for specific versions.

```
"deefour/interactor": "~0.2@dev"
```

**`>=PHP5.5.0` is required.**

## What is an Interactor

An interactor is a simple, single-purpose object.

Interactors are used to encapsulate your application's [business logic](http://en.wikipedia.org/wiki/Business_logic). Each interactor represents one thing that your application does.

## Context

An interactor runs based on a given context. The context contains everything the interactor needs to do its work.

When an interactor performs its single purpose, it may affect the passed context.

### Adding to the Context

As an interactor runs it can add information to the context. Within the scope of an interactor this can be achieved via a fluent interface on the context object.

```php
$car = new Car;

$this->context()->car = $car;
```

## Status

An interactor carries a status object. By default there is a `Success` status and an `Error` status. Interactors are given a successful status to start.

### Failing the Interactor

When something goes wrong in your interactor, you can flag the process as failed.

```php
$this->fail();

// or

$this->fail('Some explicit error message here');
```

This swaps out the `Success` status for a new `Error` status. You can ask the interactor if its state is currently successful/passing *(not failed)*.

```php
$this->ok(); // true

$this->fail();

$this->ok(); // false

echo get_class($this->status()); // 'Deefour\Interactor\Status\Error'
```

## An Example

Lets look at an interactor to create a new `Car`.

```php
namespace App\Interactors;

use Deefour\Interactor\Interactor;

class CreateCarInteractor extends Interactor {

  public function __construct(CreateCar $context = null) {
    parent::__construct($context);
  }

  public function perform() {
    $c = $this->context();

    $car = new Car([
      'make'  => $c->make,
      'model' => $c->model
    ]);

    $c->car = $car;

    if ( ! $car->save()) $this->fail();

    return $this;
  }

}
```

This interactor above has a constructor with an argument typehinted with a class that subclasses `Deefour\Interactor\Context`. This sets the expectation that the interactor will only accept a context of the specified type, even when using the public `setContext()` method after instantiation.

The interactor above also expects the context to have a `make` and `model` bound to it. Lets make a context object that requires these attributes.

```php
namespace App\Contexts;

use Deefour\Interactor\Context;

class CreateCar extends Context {

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

This will pass an array like `[ 'make' => 'Honda', 'model' => 'Accord' ]` up to the base context where assignment to the public properties will be performed.

> It's good practice to explicitly define public properties for the arguments you want exposed to your interactor.

Within a controller, lets instantiate the interactor, passing in the built context.

```php
public function create(CreateRequest $request) {
  $context    = new CreateCar($request->get('make'), $request->get('model'));
  $interactor = new CreateCarInteractor($context)->perform();

  if ($context->ok()) {
    echo 'Wow! Nice new ' . $context->car->make;
  } else {
    echo 'ERROR: ' . $interactor->status()->error();
  }
}
```

### Integration with Laravel

There are two traits that can be used in Laravel development.

##### `Deefour\Interactor\Traits\PerformsInteractors`

When used in a controller, exposes an `interactor()` and `perform()` method.

```php
use Deefour\Interactor\Traits\PerformsInteractors;
```

#### Refactoring the Previous Example

The `create()` method from the previous example can be cleaned up slightly, using the `perform()` method. This will transparently instantiate the `CreateCarInteractor` and pass the context into it.

```php
namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Contexts\CreateCar;

class CarsController extends Controller {

  use Deefour\Interactor\Traits\PerformsInteractors;

  // ...

  public function create(CreateRequest $request) {
    $context = new CreateCar($request->get('make'), $request->get('model'));

    $interactor = $this->interactor($context)->perform();

    if ($interactor->ok()) {
      echo 'Wow! Nice new ' . $context->car->make;
    } else {
      echo 'ERROR: ' . $interactor->status()->error();
    }
  }

  // ...

}
```

### Laravel 5's Command Bus and Events

Using this concept of interactors and contexts within Laravel 5's command bus and event system is simple. First, some terminology:

| `Deefour\Interactor` | Laravel          |
|----------------------|------------------|
| Context              | Event or Command |
| Interactor           | Handler          |

Next, modify your events, commands, and handlers to extend the respective `Deefour\Interactor` classes.

 - The base `App\Events\Event` and `App\Commands\Command` classes should be changed to extend `Deefour\Interactor\Context`.
 - All event and command handlers should extend `Deefour\Interactor\Interactor`.

That's it. Treat your handlers within Laravel as you would plain interactors outside of Laravel.

 1. Typehint the context in the constructor.
 2. Define a `perform()` method.

> __Note:__ You should only define Laravels' expected `handle()` method if you understand the consequences. `Deefour\Interactor\Interactor` defines a generic, Laravel-compatible `handle()` method that accepts any `Deefour\Interactor\Context` subclass and sets the interactor up with it for you.

#### A Command Handler Example

Here's an implementation of the `CreateCarInteractor` example above using Laravel 5's command handlers.

```php
namespace App\Handlers\Commands;

use App\Commands\CreateCar;
use Illuminate\Queue\InteractsWithQueue;
use Deefour\Interactor\Interactor;

class CreateCarHandler extends Interactor {

  /**
   * Initialize the command handler.
   *
   * @param  CreateCar  $command  [optional]
   */
  public function __construct(CreateCar $context = null) {
    // ...
  }

  /**
   * Handle the command.
   *
   * @return void
   */
  public function perform() {
    // Do something here...
  }

}
```

Within a controller action the `CreateCar` command can be dispatched as expected

```
namespace App\Http\Controllers;

use App\Commands\CreateCar;

class CarController extends BaseController {

  public function create() {
    $i = $this->dispatch(CreateCar::class);

    if ($i->ok()) {
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

#### 0.3.0 - January 3, 2015

 - Refactor, striping out Illuminate support.
 - Compatibility changes to work easily with Laravel 5's new command bus and event handlers.
 - Inverting resolution lookup; contexts now resolve interactors instead of the other way around.

#### 0.1.0 - October 2, 2014

 - Initial release

## License

Copyright (c) 2014 [Jason Daly](http://www.deefour.me) ([deefour](https://github.com/deefour)). Released under the [MIT License](http://deefour.mit-license.org/).








