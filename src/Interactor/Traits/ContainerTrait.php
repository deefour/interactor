<?php namespace Deefour\Interactor\Traits;

/**
 * Intended to be used within individual interactors, this makes the service
 * objects a little more "Laravel friendly" by providing access to the IoC
 * container and easy access to the current user object
 */
trait ContainerTrait {

  protected $auth;

  /**
   * The container instance.
   *
   * @var  \Illuminate\Container\Container $container
   */
  protected $container;


  /**
   * Setter for the authentication backend configured through Laravel's IoC container
   *
   * @param  \Illuminate\Auth\AuthManager
   * @return \Deefour\Interactor\Interactor
   */
  public function setAuthManager(AuthManager $auth) {
    $this->auth = $auth;

    return $this;
  }

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
   * Convenient access to the currently-logged-in user object within Laravel's
   * configured AuthManager.
   *
   * @return  \App\User
   */
  protected function user() {
    return $this->auth()->user();
  }

}
