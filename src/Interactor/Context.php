<?php namespace Deefour\Interactor;

use Illuminate\Support\Fluent;
use ReflectionClass;

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
class Context extends Fluent {

  /**
   * Override constructor for Fluent, adding a little intelligence to determine
   * if this constructor itself has been overridden by a type-hinted constructor
   * in the class extending this abstract implementation.
   *
   * If this constructor is overridden by the extending context object with a
   * (usually) type-hinted, specific set of arguments - as a way of defining
   * requirements for the interactor - those arguments will be available as
   * public properties (via magic getters) through Fluent.
   *
   * If the constructor is NOT overridden, the array handled by this abstract
   * class' constructor will be keyed as "attributes" on the object through
   * Fluent.
   *
   * The determination whether the constructor has been overridden or not is done
   * via reflection.
   *
   * {@inheritdoc}
   */
  public function __construct($attributes = array()) {
    if ($this->isConstructorOverridden()) {
      $attributes = [ 'attributes' => $attributes ];
    }

    parent::__construct($attributes);
  }



  /**
   * Determine if the constructor in this abstract context object has been
   * overridden by the extending context object to defined type-hinted arguments
   * as requirements for the interactor
   *
   * @return boolean
   */
  private function isConstructorOverridden() {
    $reflection  = new ReflectionClass(static::class);
    $constructor = $reflection->getMethod('__construct');

    return self::class !== $constructor->class;
  }

}
