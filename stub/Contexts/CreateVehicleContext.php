<?php

namespace Deefour\Interactor\Stub\Contexts;

use Deefour\Interactor\Context;
use Deefour\Interactor\Stub\Models\User;

class CreateVehicleContext extends Context
{
    public $user;

    public $vin;

    public function __construct(User $user, $vin)
    {
        $this->user = $user;
        $this->vin  = $vin;

        parent::__construct([ 'called' => false ]);
    }
}
