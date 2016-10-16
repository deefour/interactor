# Interactor

[![Build Status](https://travis-ci.org/deefour/interactor.svg)](https://travis-ci.org/deefour/interactor)
[![Packagist Version](http://img.shields.io/packagist/v/deefour/interactor.svg)](https://packagist.org/packages/deefour/interactor)
[![Code Climate](https://codeclimate.com/github/deefour/interactor/badges/gpa.svg)](https://codeclimate.com/github/deefour/interactor)
[![License](https://poser.pugx.org/deefour/interactor/license)](https://packagist.org/packages/deefour/interactor)

Simple PHP Service Objects. Inspired by [collectiveidea/**interactor**](https://github.com/collectiveidea/interactor).

## Getting Started

Run the following to add Interactor to your project's `composer.json`. See [Packagist](https://packagist.org/packages/deefour/interactor) for specific versions.

```bash
composer require deefour/interactor
```

**`>=PHP5.6.0` is required.**

## What is an Interactor

An interactor is a simple, single-purpose object.

Interactors are used to encapsulate your application's [business logic](http://en.wikipedia.org/wiki/Business_logic). Each interactor represents one thing that your application does.

An interactor

 1. Extends `Deefour\Interactor\Interactor`
 2. Implements a `call()` method

As a simple example, the below interactor creates a new `Car`.

```php
use Deefour\Interactor\Interactor;

class CreateCar extends Interactor
{
    public function call()
    {
        $c = $this->context();

        $c->car = new Car([ 'make' => $c->make, 'model' => $c->model ]);

        if ( ! $c->car->save()) {
            $this->fail('Creating the car failed!');
        }
    }
}
```

## Context

An interactor runs based on a given context. The context contains the information the interactor needs to do its work. An interactor may affect its passed context, providing data from within the interactor back to the caller.

All contexts extend the `Deefour\Transformer\MutableTransformer` from the [`deefour/transformer`](https://github.com/deefour/transformer) package. The `MutableTransformer` provides conveient access and mutation of the underlying data, including but not limited to implementations of `ArrayAccess` and `JsonSerializable`.

### Accessing the Context

An interactor's context can be accessed via the `context()` method.

```php
$this->context();
$this->context()->make; //=> 'Honda'
```

### Modifying the the Context

An interactor can add or modify the context.

```php
$this->context()->car = new Car;
```

This can be very useful to provide data back to the caller.

### Permitted Attributes

Performing safe mass assignment is easy thanks to the `MutableTransformer`'s  `only()` method.

```php
$car       = new Car;
$permitted = $this->context()->only($this->car->getFillable());

$car->fill($permitted);

$car->save();
```

### Specific Context Requirements

The default context constructor expects a single array of attributes as key/value pairs.

```php
public function __construct(array $attributes = [])
{
    $this->attributes = $attributes;
}
```

An interactor often requires specific data from the provided context. For example, a `CreateCar` interactor might expect to assign an owner to the `Car` it creates. A context class should be created specifically for this Interactor requiring a user be provided during instantiation.

```php
use Deefour\Interactor\Context;

class CarContext extends Context
{
    /**
     * The owner of the vehicle.
     *
     * @var User
     */
    public $user;

    /**
     * Constructor.
     *
     * @param User  $user
     * @param array $attributes
     */
    public function __construct(User $user, array $attributes = [])
    {
        $this->user = $user;

        parent::__construct($attributes);
    }
}
```

The `CreateCar` interactor should expect an instance of this new `CarContext` during instantiation

```php
public function __construct(CarContext $context)
{
    parent::__construct($context);
}
```

### The Context Factory

While instantiating contexts manually is the norm, the `ContextFactory` is a useful alternative in certain circumstances. Pass a fully qualified name of the context to be instantiated along with a set of attributes/parameters to the `create()` method.

```php
use App\User;
use Deefour\Interactor\ContextFactory;

$user       = User::find(34);
$attributes = [ 'make' => 'Honda', 'model' => 'Accord' ];

$context = ContextFactory::create(CarContext::class, compact('user', 'attributes'));

$context->user->id; //=> 34
$context->make;     //=> Honda
```

Explicitly specifying an `'attributes'` parameter isn't necessary. Any keys in the array of source data passed to the factory that do not match the name of a parameter on the constructor will be pushed into an `$attributes` parameter. If you provide an `'attributes'` parameter manually in addition to extra data, the extra data will be merged into the `$attributes` array.

> **Note:** Taking advantage of this requires an `$attributes` parameter be available on the constructor of the context class being instantiated through the factory.

```php
use Deefour\Interactor\ContextFactory;

$user = User::find(34);
$data = [ 'make' => 'Honda', 'model' => 'Accord' ];

$context = ContextFactory::create(CarContext::class, array_merge(compact('user'), $data));

$context->make;  //=> Toyota
$context->model; //=> Accord
$context->foo;   //=> bar
```

## Status

The state of an interactor is considered passing or failing. A context holds the current state in a `$status` proprerty. This library provides a `Success` and `Error` status. Contexts are given a `Success` status by default.

### Failing the Context

An interactor can be considered a failure when something goes wrong during it's execution. This is done by marking the context as having failed.

```php
$this->context()->fail();
```

A message can be provided to explain the reason for the failure.

```php
$this->context()->fail('Some explicit error message here');
```

**Failing a context causes a `Deefour\Interactor\Exception\Failure` exception to be thrown.**

If an exception is provided it will be thrown after copying it's message over to the `Error` status set on the context.

```php
try {
    $this->context()->fail(new CarCreationException('Invalid make/model combination'));
} catch (CarCreationException $e) {
    (string)$this->context()->status(); //=> Invalid make/model combination
}
```

### Checking the Status

You can ask if the state is currently successful/passing.

```php
$c = $this->context();

$c->ok();     //=> true
$c->status(); //=> Deefour\Interactor\Status\Success

try {
    $c->fail('Oops!');
} catch (\Deefour\Interactor\Exception\Failure $e) {
    $c->ok();             //=> false
    $c->status();         //=> Deefour\Interactor\Status\Error
    (string)$c->status(); //=> Oops!
}
```

## Usage

Within a controller, implementing the car creation through the `CreateCar` interactor might look like this.

```php
public function create(CreateRequest $request)
{
    $context = new CarContext($request->user(), $request->only('make', 'model'));

    (new CreateCar($context))->call();

    if ($context->ok()) {
        echo 'Wow! Nice new ' . $context->car->make;
    } else {
        echo 'ERROR: ' . $context->status()->error();
    }
}
```

### Dispatching Interactors

A `Deefour\Interactor\DispatchesInteractors` trait can be included in any class to reduce the creation and execution of an interactor to a single method call. In the example below, this trait will

 1. Resolve a new `CarContext` instance using the provided `User`, `'make'`, and `'model'`
 2. Instantiate a new `CreateCar` instance with the newly created `CarContext`
 3. Execute the `call()` method on the interactor
 4. Return the context

```php
namespace App\Controllers;

use App\Interactors\CreateCar;
use App\Contexts\CarContext;
use Deefour\Interactor\DispatchesInteractors;

class CarController extends BaseController
{
    use DispatchesInteractors;

    /**
     * Create a new car.
     *
     * @param  Request $request
     * @return string
     */
    public function store(Request $request)
    {
        $context = $this->dispatchInteractor(
            CreateCar::class,
            CarContext::class,
            array_merge([ 'user' => $request->user() ], $request->only('make', 'model'))
        );

        if ($context->ok()) {
            return 'Wow! Nice new ' . $context->car->make;
        } else {
            return 'ERROR: ' . $context->status()->error();
        }
    }
}
```

## Organizers

Complex scenarios may require the use of multiple interactors in sequence. If a registration form asks for a user's email, password, and VIN of their car, the submission will register a new user account and create a new vehicle for the user based on the VIN. These two actions are best broken up into a `CreateUser` and a `CreateVehicle` interactor. An organizer can be used to queue multiple interactors together.

An organizer will run through each interactor it is composed of in the order they are added. If an interactor fails, the organizer will also be considered failed, and an attempt will be made to rollback the actions performed in reverse order. The rollback will **not** be performed on the failing interactor.

### Combining Contexts via a CompositeContext

A composite context extends from the main `Deefour\Interactor\Context` class that expects to be initialized with one or more other contexts. It provides a special mapping during initialization between the passed context objects and their FQCN.

In the example above, the `CreateUser` and `CreateVehicle` interactors will respectively require a `CreateUserContext` and `CreateVehicleContext`.

```php
use Deefour\Interactor\Context;

class CreateUserContext extends Context
{
    /**
     * {@inheritdoc}
     *
     * @param User   $user
     * @param string $vin
     * @param array  $attributes
     */
    public function __construct(User $user, array $attributes)
    {
        parent::__construct($user, $attributes);
    }
}
```

```php
use Deefour\Interactor\Context;

class CreateVehicleContext extends Context
{
    /**
     * {@inheritdoc}
     *
     * @param string $vin The vin number for the vehicle.
     * @param array $attributes Additional attributes
     */
    public function __construct($vin, array $attributes)
    {
        parent::__construct(array_merge($attributes, compact('vin')));
    }
}
```


The `RegisterUser` organizer expects an instance of `RegisterUserContext`, a composite context.

```php
use Deefour\Interactor\CompositeContext;

class RegisterUserContext extends CompositeContext
{
    /**
     * Constructor.
     *
     * {@inheritdoc}
     *
     * @param CreateUserContext    $createUser
     * @param CreateVehicleContext $createVehicle
     */
    public function __construct(
        CreateUserContext $createUser,
        CreateVehicleContext $createVehicle
    ) {
        parent::__construct(func_get_args());
    }
}
```

### Combining Interactors via an Organizer

To create an organizer, extend `Deefour\Interactor\Organizer`, typehint a composite context on the constructor, and implement an `organize()` method that pushes interactors onto the queue.

```php
use Deefour\Interactor\Organizer;

class RegisterUser extends Organizer
{
    /**
     * Constructor.
     *
     * @param RegisterUserContext $context A composite context for the organizer.
     */
    public function __construct(RegisterUserContext $context)
    {
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     *
     * Create the new user and their first vehicle.
     */
    public function organize()
    {
        $this->addInteractor(new CreateUser($this->getContext(CreateUserContext::class)));
        $this->addInteractor(new CreateVehicle($this->getContext(CreateVehicleContext::class)));
    }
}
```

The `$this->getContext(...)` call is a convenient alternative to `$this->context()->get(...)`.

Unlike a normal interactor, the `call()` method on an organizer is already implemented. When called, this organizer will perform interactors in the order they were pushed onto the queue in the `organize()` method.

### Executing an Organizer

An organizer is executed like any other interactor. Call the `call()` method to kick things off after instantiation.

```php
$context = new RegisterUserContext(
    new CreateUserContext($request->all()),
    new CreateVehicleContext($request->get('vin'))
);

(new RegisterUser($context))->call();
```

### Organizer Failure and Rollback

If a failure occurs during the execution of an organizer, `rollback()` will be called on each interactor that ran successfully prior to the failure, in reverse order. Override the empty `rollback()` method on `Deefour\Interactor\Interactor` to take advantage of this.

> **Note:** The `rollback()` method is **not** called when an interactor is executed on it's own, though it can be called manually by testing for failure on the context.




### Integration With Laravel 5

A job in Laravel 5 can be treated as in interactor or organizer. The `handle()` method on a job called through Laravel's job dispatcher supports dependency injection through the [service container](http://laravel.com/docs/master/container). An implementation of the `CreateCar` interactor that takes advantage of dependency injection and Laravel's job dispatcher might look like this:

```php
namespace App\Jobs;

use App\Car;
use App\Contexts\CreateCarContext as CarContext;
use Deefour\Interactor\Interactor;
use Illuminate\Contracts\Redis\Database as Redis;

class CreateCar extends Interactor
{
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CarContext $context)
    {
        parent::__construct($context);
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle(Redis $redis)
    {
        $c      = $this->context();
        $c->car = Car::create($c->only('make', 'model'));

        $redis->publish('A new' . (string)$c->car . ' was just added to the lot!');

        return $this->context();
    }
}
```

Laravel needs to be told to to use it's own `dispatch()` method instead of the one provided by this library. This allows both interactors and vanilla Laravel jobs can be dispatched.

```php
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Deefour\Interactor\DispatchesInteractors;

class Controller
{
    use DispatchesJobs, DispatchesInteractors {
      DispatchesJobs::dispatch insteadof DispatchesInteractors;
    }
}
```

> **Note:** Interactors can even implement Laravel's `Illuminate\Contracts\Queue\ShouldQueue` to have execution deferred!

## Contribute

- Issue Tracker: https://github.com/deefour/interactor/issues
- Source Code: https://github.com/deefour/interactor

## Changelog

#### 1.2.0 - October 16, 2016

 - Exceptions can now be passed to a context's `fail()` method. A passed exception will be thrown instead of a new `Deefour\Interactor\Exception\Failure`. Thanks to [@gpassarelli](https://github.com/gpassarelli) in [#6](https://github.com/deefour/interactor/pull/6).

#### 1.1.0 - October 19, 2015

 - `call()` is no longer abstract; it's left as a blank stub to be overridden. This eliminates the need to define `call()` when another method is being used for business logic *(ie. when using a `handle()` to work with Laravel's command bus)*.
 - **Breaking Chang** The `DispatchesInteractors` trait no longer resolves interactors through the service container.
   - The trait can now be used outside the context of Laravel. A basic `dispatch()` method has been implemented to execute the `call()` method on the interactor.
   - Within the context of Laravel, all dependency injection should be done on the `handle()` method, just like any other Laravel job. **Constructor injection is no longer supported.**
   - Laravel must be told to use the `Illuminate\Foundation\Bus\DispatchesJobs::dispatch()` method when using both Laravel's job dispatcher and this library's interactor dispatcher. See **[Integration with Laravel](#integration-with-laravel-5)** above for more information.

#### 1.0.0 - October 7, 2015

 - Release 1.0.0.

#### 0.7.0 - June 21, 2015

 - New `Organizer` and `CompositeContext` for grouping interactors together.

#### 0.6.2 - June 5, 2015

 - Now following PSR-2.

#### 0.6.0 - May 30, 2015

 - New `ContextFactory` for creating context objects.
 - Now has `deefour/transformer` as dependency.
 - `Context` now exteds `MutableTransformer`. This class no longer implements `ArrayAccess` directly.
 - `attributes()` method on `Context` has been removed. Use `all()` or `raw()` *(for non-transformed version of attributes)* instead.
 - `Interactor` has been simplified, using only type-hints to enforce proper context for an interactor.

#### 0.5.0 - May 25, 2015

 - Now suggesting `deefour/transformer` be required. If available, the context will be wrapped in a `MutableTransformer`, providing all the functionality available in [`deefour/transformer`](https://github.com/deefour/transformer) transparently on the context object.
 - New `__isset()` implementation and better support for null context values.
 - Improved code formatting.

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
