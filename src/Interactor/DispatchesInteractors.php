<?php namespace Deefour\Interactor;

use Deefour\Interactor\Exception\Failure;

trait DispatchesInteractors {

  /**
   * Injects context into the command object (the interactor), and passes it on
   * through Laravel's Command Bus.
   *
   * All failures are currently suppressed.
   *
   * @param  \Deefour\Interactor\Interactor|string  $interactor
   * @param  \Deefour\Interactor\Context|array  $context  [optional]
   * @return \Deefour\Interactor\Context
   */
  public function dispatchInteractor($interactor, $context = null) {
    if ( ! is_a($interactor, Interactor::class)) {
      $interactor = app()->make($interactor, [ 'context' => $context ]);
    } elseif ( ! is_null($context)) {
      $interactor->setContext($context);
    }

    try {
      $this->dispatch($interactor);
    } catch (Failure $e) { }

    return $interactor->context();
  }

}
