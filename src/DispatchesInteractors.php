<?php

namespace Deefour\Interactor;

use Deefour\Interactor\Exception\Failure;

trait DispatchesInteractors
{
    /**
     * Injects context into the interactor and calls [call] on it.
     *
     * All failures are currently suppressed.
     *
     * @param Interactor|string $interactor
     * @param Context|array     $context
     * @param array             $attributes
     *
     * @return Context
     */
    public function dispatchInteractor($interactor, $context = Context::class, array $attributes = [])
    {
        $context    = ContextFactory::create($context, $attributes);
        $interactor = new $interactor($context);

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
     * @param mixed $interactor
     *
     * @return mixed
     */
    public function dispatch($interactor)
    {
        $interactor->call();
    }
}
