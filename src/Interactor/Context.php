<?php namespace Deefour\Interactor;

use ArrayAccess;
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
class Context implements ArrayAccess {

  /**
   * Object representing the current state of the interactor (passing/failing)
   *
   * @var Contract\Status
   */
  protected $status;

  /**
   * The attributes set on the context.
   *
   * @var array
   */
  protected $attributes = [];



  /**
   * If this constructor is overridden by the extending context object with a
   * (usually) type-hinted, specific set of arguments - as a way of defining
   * requirements for the interactor - those arguments will be available as
   * public attributes.
   *
   * @param  array  $attributes  [optional]
   */
  public function __construct(array $attributes = []) {
    $this->attributes = $attributes;
  }



  /**
   * Getter for the current status/state of the interactor
   *
   * @return Contract\Status
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
   * @param  Contract\Status  $status
   * @return Context
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
   * @return Interactor
   */
  public function fail($message = null) {
    $status = new Error($this, $message);

    $this->setStatus($status);

    throw new Failure($this, $message);
  }


  /**
   * {@inheritdoc}
   */
  public function offsetExists($offset) {
    return array_key_exists($offset, $this->attributes);
  }

  /**
   * {@inheritdoc}
   */
  public function offsetGet($offset) {
    if ( ! $this->offsetExists($offset)) {
      return null;
    }

    return $this->attributes[$offset];
  }

  /**
   * {@inheritdoc}
   */
  public function offsetSet($offset, $value) {
    $this->attributes[$offset] = $value;
  }

  /**
   * {@inheritdoc}
   */
  public function offsetUnset($offset) {
    unset($this->attributes[$offset]);
  }

  /**
   * {@inheritdoc}
   */
  public function get($attribute) {
    if ( ! isset($this->attributes[$attribute])) {
      return null;
    }
  }

  /**
   * Return the array of attributes set on the context.
   */
  public function toArray() {
    return $this->attributes;
  }

  /**
   * Retrieve a specific subset of the attributes on the context based on the
   * provided whitelist.
   *
   * @param  array  $whitelist
   * @return array
   */
  public function permit(array $whitelist, $attributes = null) {
    $attributes     = $attributes ?: $this->attributes;
    $response = [];

    foreach ($whitelist as $key => $value) {
      if (is_string($value)) { // scalar value
        $this->addPermittedValue($response, $attributes, $value);
      } elseif ( ! is_array($value)) { // invalid structure; move on
        continue;
      } elseif (empty($value)) { // arbitrary array/collection
        $this->addPermittedCollection($response, $attributes, $key);
      } else { // recursion
        $response[$key] = $this->permit($whitelist[$key], $attributes[$key]);
      }
    }

    return $response;
  }


  /**
   * Adds a specific attribute to the resposne object.
   *
   * @param  array  $response
   * @param  array  $source
   * @param  string  $attribute
   * @return void
   */
  private function addPermittedValue(array &$response, array $source, $attribute) {
    if ( ! isset($source[$attribute])) {
      return;
    }

    $response[$attribute] = $source[$attribute];
  }

  /**
   * Adds an arbitrary collection to the response object, by key.
   *
   * @param  array  $response
   * @param  array  $source
   * @param  string  $attribute
   * @return void
   */
  private function addPermittedCollection(array &$response, array $source, $attribute) {
    if ( ! isset($source[$attribute]) or ! is_array($source[$attribute])) {
      return;
    }

    $response[$attribute] = $source[$attribute];
  }



  /**
   * Magic access for attributes set on the context object.
   *
   * @param  string  $attribute
   * @return mixed
   */
  public function __get($attribute) {
    return $this->offsetGet($attribute);
  }

  /**
   * Magic setter, pushing values into the attributes array by property name.
   *
   * @param  string  $attribute
   * @param  mixed  $value
   */
  public function __set($attribute, $value) {
    $this->offsetSet($attribute, $value);
  }

}
