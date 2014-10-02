<?php namespace spec\Deefour\Interactor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ContextSpec extends ObjectBehavior {

  function let(FancyContext $context) {
    $this->beAnInstanceOf('spec\Deefour\Interactor\PlainContext');
  }

  function it_is_initializable() {
    $this->shouldHaveType('Deefour\Interactor\Context');
  }

}

class PlainContext extends \Deefour\Interactor\Context { }

class FancyContext extends \Deefour\Interactor\Context {

  public function __construct(array $attributes, $type) {
    parent::__construct(get_defined_vars());
  }

}