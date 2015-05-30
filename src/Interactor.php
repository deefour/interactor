<?php namespace Deefour\Interactor;

use Deefour\Interactor\Exception\Failure;

abstract class Interactor {

  /**
   * Context object containing data required to call the interactor behavior
   *
   * @var Context
   */
  protected $context = null;

  /**
   * Configure the interactor, binding a context object and defaulting the state
   * of the interactor to passing/OK.
   *
   * @param  Context $context [optional]
   */
  public function __construct(Context $context) {
    $this->context = $context;
  }

  /**
   * Getter for the context object bound to the interactor
   *
   * @return Context
   */
  public function context() {
    return $this->context;
  }

  /**
   * Convenience method to fail the interactor, passing through to the Context.
   *
   * @throws Failure
   */
  protected function fail() {
    $this->context()->fail();
  }

  /**
   * Process the business logic this interactor exists to handle, using the
   * bound context for support.
   *
   * @return void
   */
  abstract public function call();

}
