<?php namespace Deefour\Interactor;

/**
 * Abstract context object. Extends the Fluent class from illuminate/support,
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

}
