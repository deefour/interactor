<?php

namespace Deefour\Interactor\Status;

use Deefour\Interactor\Status;

/**
 * The passing/successful status object. The default status object for an
 * interactor.
 */
class Success extends Status
{
    /**
     * {@inheritdoc}
     */
    public function ok()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return 'OK';
    }
}
