<?php namespace Deefour\Interactor\Exception;

use Deefour\Interactor\Context;
use Exception;

class Failure extends Exception {

  /**
   *
   */
  protected $context;

  /**
   * Constructor
   *
   * @param  \Deefour\Interactor\Context  $context
   * @param  string  $message  [optional]
   */
  public function __construct(Context $context, $message = '') {
    $this->context = $context;

    parent::__construct($message);
  }

}
