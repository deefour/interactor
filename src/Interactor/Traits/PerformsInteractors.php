<?php namespace Deefour\Interactor\Traits;

use Deefour\Interactor\Context;

/**
 * Intended to be used within a Laravel controller, provides a quick way to
 * generate interactors with a bound context and access to the IoC container
 * and current user object, all in one line of code.
 */
trait PerformsInteractors {

  /**
   * Performs the interactor.
   *
   * @param  \Deefour\Interactor\Context  $context
   * @return \Deefour\
   */
  public function perform(Context $context) {
    $interactor = $this->interactor($context);

    $interactor->perform();

    return $interactor;
  }

  /**
   * Create a new interactor based on the specified class name, binding the passed
   * context object, as well as Laravel's IoC container for easy
   * dependency resolution and access to the current user object.
   *
   * @param  \Deefour\Interactor\Context  $context
   * @return \Deefour\Interactor\Interactor
   */
  public function interactor(Context $context) {
    $interactor = str_replace('Context', 'Interactor', get_class($context) . 'Interactor');

    return new $interactor($context);
  }

}
