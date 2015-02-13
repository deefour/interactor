<?php namespace Deefour\Interactor;

use Deefour\Interactor\Exception\Failure;
use InvalidArgumentException;

trait DispatchesInteractors {

  /**
   * Injects context into the command object (the interactor), and passes it on
   * through Laravel's Command Bus.
   *
   * All failures are currently suppressed.
   *
   * @param  Interactor|string  $interactor
   * @param  Context|array  $context  [optional]
   * @return Context
   */
  public function dispatchInteractor($interactor, $context = null) {
    if ( ! is_a($interactor, Interactor::class, true)) {
      $class = is_string($interactor) ? $interactor : get_class($interactor);

      throw new InvalidArgumentException(
        sprintf('$interactor must be an instance of \Deefour\Interactor\Interactor' .
                ' or the FQCN of an interactor class; [%s] was provided', $class));
    }

    if (is_string($interactor)) {
      $interactor = app()->make($interactor, [ 'context' => $context ]);
    } elseif ( ! is_null($context)) {
      $interactor->setContext($context);
    }

    try {
      $this->dispatch($interactor);
    } catch (Failure $e) {
      // Silently fail
    }

    return $interactor->context();
  }

  /**
   * Dispatch a command to its appropriate handler.
   *
   * @param  mixed  $command
   * @return mixed
   */
  abstract public function dispatch($command);

}
