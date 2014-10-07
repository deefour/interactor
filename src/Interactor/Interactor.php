<?php namespace Deefour\Interactor;

use Deefour\Interactor\Status\Success;
use Deefour\Interactor\Status\Error;
use Deefour\Interactor\Contract\Status as StatusContract;

abstract class Interactor {

  /**
   * Object representing the current state of the interactor (passing/failing)
   *
   * @var \Deefour\Interactor\Contract\Status
   */
  protected $status;

  /**
   * Context object containing data required to perform the interactor behavior
   *
   * @var \Deefour\Interactor\Context
   */
  protected $context;



  /**
   * Configure the interactor, binding a context object and defaulting the state
   * of the interactor to passing/OK.
   *
   * @param  \Deefour\Interactor\Context  $context
   * @return void
   */
  public function __construct(Context $context = null) {
    $this->context = $context ?: new Context;
  }

  /**
   * Getter for the current status/state of the interactor
   *
   * @return \Deefour\Interactor\Contract\Status
   */
  public function status() {
    return $this->status ?: new Success($this->context);
  }

  /**
   * Geter for the context object bound to the interactor
   *
   * @return \Deefour\Interactor\Context
   */
  public function context() {
    return $this->context;
  }

  /**
   * Setter for the context object on the interactor
   *
   * @param  \Deefour\Interactor\Context  $context
   * @return \Deefour\Interactor\Interactor
   */
  public function setContext(Context $context) {
    $this->context = $context;

    return $this;
  }

  /**
   * Quick access to check if the state of the interactor is still condisered
   * 'passing'.
   *
   * @return boolean
   */
  public function ok() {
    return $this->status() instanceof Success;
  }



  /**
   * Setter for the status object bound to the interactor
   *
   * @param  \Deefour\Interactor\Contract\Status  $status
   * @return \Deefour\Interactor\Interactor
   */
  protected function setStatus(StatusContract $status) {
    $this->status = $status;

    return $this;
  }

  /**
   * Marks the state of the interactor as failed, setting a sensible messaeg
   * to explain what went wrong.
   *
   * @param  string  $message  [optional]
   * @return \Deefour\Interactor\Interactor
   */
  protected function fail($message = null) {
    $status = new Error($this->context(), $message);

    $this->setStatus($status);

    return $this;
  }



  /**
   * Process the business logic this interactor exists to handle, using the bound
   * context for support.
   *
   * @return \Deefour\Interactor\Interactor
   */
  abstract public function perform();

}
