<?php

namespace Deefour\Interactor\Stub\Interactors;

use Deefour\Interactor\Interactor;
use Deefour\Interactor\Stub\Contexts\CreateVehicleContext;

class CreateVehicle extends Interactor
{
    public function __construct(CreateVehicleContext $context)
    {
        parent::__construct($context);
    }

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
