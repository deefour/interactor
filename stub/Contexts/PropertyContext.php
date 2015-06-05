<?php

namespace Deefour\Interactor\Stub\Contexts;

use Deefour\Interactor\Context;

class PropertyContext extends Context
{
    public $make;

    public $model;

    public function __construct($make, $model)
    {
        $this->make  = $make;
        $this->model = $model;
    }
}
