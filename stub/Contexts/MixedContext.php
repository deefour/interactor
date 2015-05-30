<?php namespace Deefour\Interactor\Stub\Contexts;

use Deefour\Interactor\Context;

class MixedContext extends Context {

  public $make;

  public $model;

  public function __construct($make, $model, array $attributes) {
    $this->make  = $make;
    $this->model = $model;

    parent::__construct($attributes);
  }

}
