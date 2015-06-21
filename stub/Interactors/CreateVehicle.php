<?php

namespace Deefour\Interactor\Stub\Interactors;

use Deefour\Interactor\Interactor;

class CreateVehicle extends Interactor
{
    public function call()
    {
        if ($this->context()->should_fail) {
            $this->fail('Oops!');
        }

        $this->context()->called = true;
    }

    public function rollback() {
        $this->context()->rolled_back = true;
    }
}
