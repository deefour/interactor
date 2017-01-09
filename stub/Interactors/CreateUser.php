<?php

namespace Deefour\Interactor\Stub\Interactors;

use Deefour\Interactor\Interactor;
use Deefour\Interactor\Stub\Models\User;

class CreateUser extends Interactor
{
    public function call()
    {
        $c = $this->context();

        $c->called = true;
        $c->user   = new User($c->first_name, $c->last_name);
    }

    public function rollback()
    {
        $this->context()->rolled_back = true;
    }
}
