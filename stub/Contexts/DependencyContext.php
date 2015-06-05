<?php

namespace Deefour\Interactor\Stub\Contexts;

use Deefour\Interactor\Context;
use Deefour\Interactor\Stub\FooDependency;

class DependencyContext extends Context
{
    public $make;

    public $model;

    public $foo;

    public function __construct($make, $model, FooDependency $foo, array $attributes = [])
    {
        $this->make  = $make;
        $this->model = $model;
        $this->foo   = $foo;
    }
}
