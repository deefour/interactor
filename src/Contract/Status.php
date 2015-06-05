<?php

namespace Deefour\Interactor\Contract;

use Deefour\Interactor\Context;

/**
 * Contract which all status objects must adhere to.
 */
interface Status
{
    /**
     * Whether or not the service object called it's action successfully.
     *
     * @return bool
     */
    public function ok();

    /**
     * Retrieve the context object from the service object that has been
     * injected into this class.
     *
     * @return Context
     */
    public function context();

    /**
     * String representation of the status object.
     *
     * @return string
     */
    public function __toString();
}
