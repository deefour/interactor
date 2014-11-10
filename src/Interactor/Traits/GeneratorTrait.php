<?php namespace Deefour\Interactor\Traits;

use Deefour\Interactor\Context;

/**
 * Intended to be used within a Laravel controller, provides a quick way to
 * generate interactors with a bound context and access to the IoC container
 * and current user object, all in one line of code.
 */
trait GeneratorTrait {

  /**
   * Create a new interactor based on the specified class name, binding the passed
   * context object, as well as Laravel's IoC container and AuthManager for easy
   * dependency resolution and access to the current user object.
   *
   * @param  string  $class  The full, namespaced, class name of the interactor to create
   * @param  \Deefour\Interactor\Context  $context  [optional]
   * @return \Deefour\Interactor\Interactor
   */
  public function interactor($class, Context $context = null) {
    if (is_null($context)) {
      $context = $interactor->resolveContext();
    }

    $interactor = $this->container->make($class, [ 'context' => $context ]);

    $interactor->setContainer($this->container)
               ->setAuthManager($this->container->make('auth'));

    return $interactor;
  }

}
