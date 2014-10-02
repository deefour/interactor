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

  function it_provides_the_status() {
    $this->status()->shouldReturnAnInstanceOf('Deefour\Interactor\Status');
  }

  function it_provides_the_context($context) {
    $this->context()->shouldReturn($context);
  }

  function it_is_ok_by_default() {
    $this->ok()->shouldReturn(true);
  }

  function it_is_not_ok_when_failure_occurs() {
    $this->perform()->ok()->shouldReturn(false);
  }

}

class FailingInteractor extends \Deefour\Interactor\Interactor {

  public function perform() {
    $this->fail('FAILURE');

    return $this;
  }

}

class InteractorContext extends \Deefour\Interactor\Context {}