<?php

namespace Deefour\Interactor\Contract;

interface Interactor
{
    /**
     * Process the business logic this interactor exists to handle, using the
     * bound context for support.
     */
    public function call();

    /**
     * Rollback changes.
     */
    public function rollback();
}
