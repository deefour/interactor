<?php namespace Deefour\Interactor;

use Deefour\Interactor\Exception\Failure;

trait DispatchesInteractors {

  /**
   * Injects context into the command object (the interactor), and passes it on
   * through Laravel's Command Bus.
   *
   * All failures are currently suppressed.
   *
   * @param  Interactor|string $interactor
   * @param  Context|array     $context [optional]
   * @param  array             $attributes
   *
   * @return Context
   */
  public function dispatchInteractor($interactor, $context = Context::class, array $attributes = [ ]) {
    $context    = ContextFactory::create($context, $attributes);
    $interactor = app()->make($interactor, compact('context'));

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
   * @param  mixed $command
   *
   * @return mixed
   */
  abstract public function dispatch($command);

}
