<?php namespace Deefour\Interactor;

use Deefour\Interactor\Exception\ContextResolution as ContextResolutionException;
use Deefour\Interactor\Exception\Failure;
use ReflectionMethod;

abstract class Interactor {

  /**
   * Context object containing data required to call the interactor behavior
   *
   * @var \Deefour\Interactor\Context
   */
  protected $context = null;



  /**
   * Configure the interactor, binding a context object and defaulting the state
   * of the interactor to passing/OK.
   *
   * @param  array|\Deefour\Interactor\Context  $context  [optional]
   */
  public function __construct($context = null) {
    $this->setContext($context);
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
   * @param  \Deefour\Interactor\Context|array  $context
   * @return \Deefour\Interactor\Interactor
   */
  public function setContext($context) {
    $contextClass = $this->contextClass();

    if ( ! is_null($contextClass) and ! is_a($context, $contextClass)) {
      throw new ContextResolutionException(sprintf('A context class of type `%s` is required for this interactor.', $contextClass));
    }

    if ( ! is_a($context, Context::class)) {
      $context = new Context($context);
    }

    $this->context = $context;

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
      if (is_null($parameter->getClass())) {
        continue;
      }

      $className = $parameter->getClass()->name;

      if (is_a($className, Context::class, true)) {
        return $className;
      }
    }

    return null;
  }



  /**
   * Process the business logic this interactor exists to handle, using the bound
   * context for support.
   *
   * @return \Deefour\Interactor\Interactor
   */
  public function call() {
    throw new \Exception('No call method is defined!');
  }

}
