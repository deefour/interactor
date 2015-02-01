<?php namespace spec\Deefour\Interactor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InteractorSpec extends ObjectBehavior {

  function let(InteractorContext $context) {
    $this->beAnInstanceOf('spec\Deefour\Interactor\FailingInteractor');
    $this->beConstructedWith($context);
  }

  function it_is_initializable() {
    $this->shouldHaveType('Deefour\Interactor\Interactor');
  }

  function it_provides_the_context($context) {
    $this->context()->shouldReturn($context);
  }

  function it_sets_context_and_calls_interactor_via_handle() {
    $context = new InteractorContext;

    $this->handle($context)->shouldReturn($context);
    $this->context()->ok()->shouldReturn(false);
  }

}



class FailingInteractor extends \Deefour\Interactor\Interactor {

  public function call() {
    $this->context()->fail('FAILURE');

    return $this;
  }

}

class PassingInteractor extends \Deefour\Interactor\Interactor {

  public function call() {
    return $this;
  }

}

class PassingContext extends \Deefour\Interactor\Context {

  public function __construct($foo, $bar) {
    parent::__construct(get_defined_vars());
  }

}

class InteractorContext extends \Deefour\Interactor\Context { }
