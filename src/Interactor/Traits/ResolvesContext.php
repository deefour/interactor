<?php namespace Deefour\Interactor\Traits;

use Deefour\Interactor\Context;
use Deefour\Interactor\Exception\ContextResolution as ContextResolutionException;
use Illuminate\Auth\AuthManager;
use Illuminate\Container\Container;
use ReflectionClass;

/**
 * Intended to be used within individual interactors, this makes the service
 * objects a little more "Laravel friendly" by providing access to the IoC
 * container and easy access to the current user object
 */
trait ResolvesContext {

  protected $auth;

  /**
   * The container instance.
   *
   * @var  \Illuminate\Container\Container $container
   */
  protected $container;


  /**
   * Setter for the container instance used to resolve dependencies.
   *
   * @param  \Illuminate\Container\Container $container
   * @return \Deefour\Interactor\Interactor
   */
  public function setContainer(Container $container) {
    $this->container = $container;

    return $this;
  }

  /**
   *
   * If a class exists with either of the replacements/mappings, an attempt will
   * be made to generate the context by creating it through Laravel's IoC container.
   *
   * This resolveContext method can also be overridden to provide a context
   * as well.
   *
   * @see \Deefour\Interactor\Traits\MakesInteractors
   * @param  mixed  $context  [optional]
   * @return \Deefour\Interactor\Context
   */
  public function resolveContext($context = null) {
    $contextName = $this->resolveContextName($context);
    $reflection  = new ReflectionClass($contextName);
    $parameters  = $reflection->getConstructor()->getParameters();
    $iocParams   = [];
    $request     = $this->container->make('request');

    foreach ($parameters as $parameter) {
      $param = $parameter->name;

      if ( ! is_null($parameter->getClass())) {
        continue;
      }

      $iocParams[$param] = $request->get($param, null);
    }

    $this->setContext($this->container->make($contextName, $iocParams));

    return $this;
  }



  /**
   * Tries to resolve a context for the interactor. The lookup happens in the
   * following order
   *
   *  1. Check if a class exists with `Context` suffix appended to the current
   *     class name
   *  2. Try to replace an `Interactor` namespace for `Context`
   *
   * @throws \Deefour\Interactor\Exception\ContextResolution
   * @param  mixed  $context  [optional]
   * @return string
   */
  protected function resolveContextName($context = null) {
    if ($context instanceof Context) {
      return get_class($context);
    }

    if (class_exists($context)) {
      return $context;
    }

    $interactorName = static::class;
    $contextName    = null;

    // try appending the 'Context' suffix
    $possibleName = $interactorName . 'Context';

    if (class_exists($possibleName)) {
      return $possibleName;
    }

    // try replacing 'Interactor' for 'Context' in namespace declaration
    $possibleName = preg_replace('#Interactor(s?)(?!.*Interactor(s?))#', 'Context\1', $interactorName);

    if (is_null($contextName) and class_exists($possibleName)) {
      return $possibleName;
    }

    throw new ContextResolutionException(sprintf('Context object for interactor `%s` could not be resolved.', static::class));
  }



  /**
   * Setter for the context object on the interactor
   *
   * @param  \Deefour\Interactor\Context  $context
   * @return \Deefour\Interactor\Interactor
   */
  public abstract function setContext(Context $context);

}
