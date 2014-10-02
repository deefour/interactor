<?php namespace Deefour\Interactor\Contract;

/**
 * Contract which all status objects must adhere to.
 */
interface Status {

  /**
   * Whether or not the service object performed it's action successfully
   *
   * @return boolean
   */
  public function ok();

  /**
   * Retrieve the context object from the service object that has been
   * injected into this class
   *
   * @return \Deefour\Interactor\Context
   */
  public function context();

  /**
   * String representation of the status object
   *
   * @return string
   */
  public function __toString();

}