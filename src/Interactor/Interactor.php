<?php namespace Deefour\Interactor;

use Deefour\Interactor\Exception\ContextResolution as ContextResolutionException;
use Deefour\Interactor\Status\Success;
use Deefour\Interactor\Status\Error;
use Deefour\Interactor\Contract\Status as StatusContract;
use ReflectionMethod;

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
   * Laravel-compatible method for setting the context (a command or event, for
   * example), and performing the interactor all at once.
   *
   * @return \Deefour\Interactor\Interactor
   */
  public function handle(Context $context) {
    $this->setContext($context);

    $this->perform();

    return $this;
  }

  /**
   * Setter for the context object on the interactor
   *
   * @param  \Deefour\Interactor\Context  $context
   * @return \Deefour\Interactor\Interactor
   */
  public function setContext(Context $context) {
    if ( ! $this->isValidContext($context)) {
      throw new ContextResolutionException(sprintf('A context class of type `%s` is required for this interactor.', $this->contextClass()));
    }

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
   * Determine the FQCN of the context class type-hinted on the constructor's
   * method signature.
   *
   * @throws \Deefour\Interactor\Exception\ContextResolution;
   * @return string
   */
  protected function contextClass() {
    $constructor = new ReflectionMethod($this, '__construct');
    $parameters  = $constructor->getParameters();

    foreach ($parameters as $parameter) {
      $className = $parameter->getClass()->name;

      if (is_a($className, Context::class, true)) {
        return $className;
      }
    }

    throw new ContextResolutionException('No context is specified on the `__construct()` method for this class.');
  }

  /**
   * Boolean check whether the passed object is an instance of or sublass of
   * the type-hinted context object specified in the constructor's method signature
   * for this interactor.
   *
   * @param  \Deefour\Interactor\Context  $context
   * @return boolean
   */
  protected function isValidContext($context) {
    return is_a($context, $this->contextClass());
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



  /**
   * Process the business logic this interactor exists to handle, using the bound
   * context for support.
   *
   * @return \Deefour\Interactor\Interactor
   */
  abstract public function perform();

}
