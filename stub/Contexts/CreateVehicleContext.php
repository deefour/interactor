<?php

namespace Deefour\Interactor\Stub\Contexts;

use Deefour\Interactor\Context;

class CreateVehicleContext extends Context
{
    public $make;

    public $model;

    public function __construct($make, $model)
    {
        $this->make  = $make;
        $this->model = $model;
        
        parent::__construct([ 'called' => false ]);
    }
}
