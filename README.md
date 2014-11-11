# Interactor

[![Build Status](https://travis-ci.org/deefour/interactor.svg)](https://travis-ci.org/deefour/interactor)
[![Packagist Version](http://img.shields.io/packagist/v/deefour/interactor.svg)](https://packagist.org/packages/deefour/interactor)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/deefour/interactor/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/deefour/interactor/?branch=master)

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

An interactor is given a context. The context contains everything the interactor needs to do its work.

When an interactor does its single purpose, it may affect its given context.

### Adding to the Context

As an interactor runs it can add information to the context.

```php
$car = new Car;

$this->context()->car = $car;
```

## Status

An interactor carries a status object. By default there is a `Success` status and an
`Error` status. Interactors are given a successful status to start.

### Failing the Interactor

When something goes wrong in your interactor, you can flag the process as failed.

```php
$this->fail();
```

This swaps out the `Success` status for a new `Error` status. You can ask the interactor if its state is currently successful/passing *(not failed)*.

```php
$this->ok(); // true

$this->fail();

$this->ok(); // false

echo get_class($this->context()); // 'Deefour\Interactor\Status\Error'
```

## An Example

Lets look at an interactor to create a new `Car`.

```php
use Deefour\Interactor\Interactor;

class CreateCar extends Interactor {

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

This interactor expects the context to have a `make` and `model` bound to it. Lets make a context object that requires these attributes.

```php
use Deefour\Interactor\Context;

class CreateCarContext extends Context {

  public function __construct($make, $model) {
    parent::__construct(get_defined_vars());
  }
}
```

This will pass an array like `[ 'make' => 'Honda', 'model' => 'Accord' ]` up to the base context.

> Read about [`get_defined_vars()`](http://php.net/manual/en/function.get-defined-vars.php). Internally this is fed into an `Illuminate\Support\Fluent` object, providing array and property access to `make` and `model` directly on the context object.

Lets then require the interactor use this specific implementation of the context object by overriding the default constructor.

```php
use Deefour\Interactor\Interactor;

class CreateCar extends Interactor {

  public function __construct(CreateCarContext $context) {
    parent::__construct($context);
  }

  public function perform() {
    // ...
  }

}
```

Finally, inside a controller, lets instantiate the interactor, passing in the built context.

```php
public function create(CreateRequest $request) {
  $context    = new CreateCarContext($request->get('make'), $request->get('model'));
  $interactor = new CreateCar($context)->perform();

  if ($context->ok()) {
    echo 'Wow! Nice new ' . $context->car->make;
  } else {
    echo 'ERROR: ' . $interactor->status()->error();
  }
}
```

### Integration with Laravel

There are two traits that can be used in Laravel development.

##### `Deefour\Interactor\Traits\MakesInteractors`

When used in a controller, exposes an `interactor()` method.

```php
use Deefour\Interactor\Traits\MakesInteractors;
```

##### `Deefour\Interactor\Traits\ResolvesDependencies`

When used in an interactor, exposes a `user()` method and provides access to Laravel's [IoC container](http://laravel.com/docs/master/container).

```php
use Deefour\Interactor\Traits\ResolvesDependencies;
```

#### Refactoring the Previous Example

The `create` method from the previous example can be cleaned up slightly, using the `interactor()` method.

```php
namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Interactors\Car\Create as CreateInteractor;

class CarsController extends Controller {

  use Deefour\Interactor\Traits\MakesInteractors;

  // ...

  public function create(CreateRequest $request) {
    $make    = $request->get('make');
    $model   = $request->get('model');
    $context = new CreateContext($make, $model);

    $interactor = $this->interactor(CreateInteractor::class, $context)->perform();

    if ($interactor->ok()) {
      echo 'Wow! Nice new ' . $context->car->make;
    } else {
      echo 'ERROR: ' . $interactor->status()->error();
    }
  }

  // ...

}
```

Passing a context is optional. If omitted from the `interactor()` method call, the `MakesInteractors` will try to determine which context object to create, and try instantiating it with information from the request object.

This means the above example could be refactored further. This example also moves the trait into a new `App\Http\Controllers\BaseController` which makes the `interactor()` method available to all controllers at once.

```php
namespace App\Http\Controllers;

use App\Interactors\Car\Create as CreateInteractor;

class CarsController extends BaseController {

  public function create(CreateRequest $request) {
    $i = $this->interactor(CreateInteractor::class)->perform();

    if ($i->ok()) {
      echo 'Wow! Nice new ' . $i->context()->car->make;
    } else {
      echo 'ERROR: ' . $i->status()->error();
    }
  }

}
```

#### Automatic Context Resolution Details

Given a `App\Interactors\Car\Create` class, the resolution will look for either of the following classes.

 - `App\Interactors\Car\CreateContext`
 - `App\Contexts\Car\Create`

A `Deefour\Interactor\Exception\ContextResolution` exception will be thrown if the context object could not be determined or instantiated.

The existing context object will be instantiated through Laravel's IoC container. Constructor arguments that are not type-hinted will have the request parameter with the same name as the argument pulled from Laravel's `Request` object. For example

```php
public function __construct($make, $model, CarPolicy $policy) {
    // ...
}
```

This will fetch `'make'` and `'model'` from the request object, passing each value into the constructor's respective arguments. An attempt will be made to resolve the `CarPolicy` argument through Laravel's IoC container.

## Contribute

- Issue Tracker: https://github.com/deefour/interactor/issues
- Source Code: https://github.com/deefour/interactor

## Changelog

#### 0.1.0 - October 2, 2014

 - Initial release

## License

Copyright (c) 2014 [Jason Daly](http://www.deefour.me) ([deefour](https://github.com/deefour)). Released under the [MIT License](http://deefour.mit-license.org/).








