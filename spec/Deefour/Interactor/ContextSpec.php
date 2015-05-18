<?php namespace spec\Deefour\Interactor;

use Deefour\Transformer\MutableTransformer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ContextSpec extends ObjectBehavior {

  function let(FancyContext $context) {
    $this->beAnInstanceOf('spec\Deefour\Interactor\PlainContext');
  }

  function it_is_initializable() {
    $this->shouldHaveType('Deefour\Interactor\Context');
  }

  function it_provides_the_status() {
    $this->status()->shouldReturnAnInstanceOf('Deefour\Interactor\Status');
  }

  function it_is_ok_by_default() {
    $this->ok()->shouldReturn(true);
  }

  function it_is_not_ok_when_failure_occurs() {
    $this->shouldThrow('\Deefour\Interactor\Exception\Failure')->duringFail();
    $this->ok()->shouldReturn(false);
  }

  function it_allows_access_to_public_methods_via_properties() {
    $this->__get('ok')->shouldReturn(true);
  }

  function it_returns_null_for_unknown_properties() {
    $this->__get('asdf')->shouldReturn(null);
  }

  function it_returns_raw_attributes_object_via_accessor() {
    $this->attributes()->shouldReturnAnInstanceOf('Deefour\Transformer\MutableTransformer');
  }

}

class PlainContext extends \Deefour\Interactor\Context { }

class FancyContext extends \Deefour\Interactor\Context {

  public function __construct(array $attributes, $type) {
    parent::__construct(get_defined_vars());
  }

}
