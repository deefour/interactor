<?php namespace Deefour\Interactor\Traits;

use Deefour\Interactor\Context;
use Illuminate\Support\Facades\Facade;

/**
 * Intended to be used within a Laravel controller, provides a quick way to
 * generate interactors with a bound context and access to the IoC container
 * and current user object, all in one line of code.
 */
trait PerformsInteractors {

  /**
   * Performs the interactor.
   *
   * @param  string  $name  The full, namespaced, class name of the interactor to create
   * @param  \Deefour\Interactor\Context  $context  [optional]
   * @return \Deefour\
   */
  public function perform($name, Context $context  = null) {
    $interactor = $this->interactor($name, $context);

    $interactor->perform();

    return $interactor;
  }

  /**
   * Create a new interactor based on the specified class name, binding the passed
   * context object, as well as Laravel's IoC container for easy
   * dependency resolution and access to the current user object.
   *
   * @param  string  $name  The full, namespaced, class name of the interactor to create
   * @param  \Deefour\Interactor\Context  $context  [optional]
   * @return \Deefour\Interactor\Interactor
   */
  public function interactor($name, Context $context = null) {
    $container  = Facade::getFacadeApplication();
    $interactor = new $name($context);

    $interactor->setContainer($container);

    if ( ! $interactor->context()) {
      $interactor->resolveContext();
    }

    return $interactor;
  }

}
