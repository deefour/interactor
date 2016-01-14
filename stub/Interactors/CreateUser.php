<?php

namespace Deefour\Interactor\Stub\Interactors;

use Deefour\Interactor\Interactor;

class CreateUser extends Interactor
{
    public function call()
    {
        $this->context()->called = true;
    }

    public function rollback()
    {
        $this->context()->rolled_back = true;
    }
}
