<?php namespace Deefour\Interactor;

use Deefour\Interactor\Exception\Failure;
use Deefour\Interactor\Status\Success;
use Deefour\Interactor\Status\Error;
use Deefour\Interactor\Contract\Status as StatusContract;
use ReflectionMethod;

/**
 * Context object. Extends the Fluent class from illuminate/support,
 * making the creation and use of individual DTO's a breeze.
 *
 * The expectation is that the context objects extending this abstract class'
 * default behavior will be to provide a type-hinted constructor signature,
 * passing the arguments up into this abstract class' constructor as a single,
 * associative array.
 *
 * Example:
 *
 * <code>
 * public function __constuct(array $attributes, User $user) {
 *   parent::__construct(get_defined_vars());
 * }
 * </code>
 */
class Context {

  /**
   * Object representing the current state of the interactor (passing/failing)
   *
   * @var \Deefour\Interactor\Contract\Status
   */
  protected $status;



  /**
   * If this constructor is overridden by the extending context object with a
   * (usually) type-hinted, specific set of arguments - as a way of defining
   * requirements for the interactor - those arguments will be available as
   * public properties.
   *
   * @param  array  $properties  [optional]
   */
  public function __construct(array $properties = []) {
    foreach ($properties as $property => $value) {
      $this->$property = $value;
    }
  }



  /**
   * Getter for the current status/state of the interactor
   *
   * @return \Deefour\Interactor\Contract\Status
   */
  public function status() {
    return $this->status ?: new Success($this);
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
  public function fail($message = null) {
    $status = new Error($this, $message);

    $this->setStatus($status);

    throw new Failure($this, $message);
  }



  /**
   * Magic method invocation via property access for public methods.
   *
   * Example
   *
   *   $interactor->ok; //=> true
   *
   * @param  string  $arg
   * @return mixed
   */
  public function __get($arg) {
    if (method_exists($this, $arg) and (new ReflectionMethod($this, $arg))->isPublic()) {
      return call_user_func([$this, $arg]);
    }

    return null;
  }

}
