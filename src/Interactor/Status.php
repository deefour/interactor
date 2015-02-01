<?php namespace Deefour\Interactor;

use Deefour\Interactor\Context;
use Deefour\Interactor\Contract\Status as StatusContract;
use JsonSerializable;

abstract class Status implements StatusContract, JsonSerializable {

  /**
   * The context injected into the interactor that generated this status object
   *
   * @var \Deefour\Interactor\Context
   */
  protected $context;



  /**
   * Configure the status, injecting the context from the interactor
   *
   * @param  \Deefour\Interactor\Context  $context
   */
  public function __construct(Context $context) {
    $this->context = $context;
  }



  /**
   * Getter for the bound context
   *
   * @return \Deefour\Interactor\Context
   */
  public function context() {
    return $this->context;
  }

  /**
   * {@inheritdoc}
   */
  public function jsonSerialize() {
    return [ 'status' => (string)$this ];
  }

}
