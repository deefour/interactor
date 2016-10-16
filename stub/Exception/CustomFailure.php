<?php

namespace Deefour\Interactor\Stub\Exception;

use Exception;

class CustomFailure extends Exception
{
    public function __construct()
    {
        parent::__construct('Custom failure message.');
    }
}
