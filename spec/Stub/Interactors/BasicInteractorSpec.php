<?php namespace spec\Deefour\Interactor\Stub\Interactors;

use Deefour\Interactor\Exception\Failure;
use Deefour\Interactor\Status\Error;
use Deefour\Interactor\Stub\Contexts\PlainContext;
use Deefour\Interactor\Stub\Interactors\BasicInteractor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BasicInteractorSpec extends ObjectBehavior {

  function let() {
    $this->beAnInstanceOf(BasicInteractor::class);
    $this->beConstructedWith(new PlainContext);
  }

  function it_provides_the_context() {
    $this->context()->shouldBeAnInstanceOf(PlainContext::class);
  }

  function it_allows_failure_passthru_on_the_interactor() {
    $this->shouldThrow(Failure::class)->during('call', [ true ]);
    $this->context()->ok()->shouldReturn(false);
    $this->context()->status()->shouldBeAnInstanceOf(Error::class);
  }


  function it_has_passing_context_by_default() {
    $this->callOnWrappedObject('call');
    $this->context()->ok()->shouldReturn(true);
  }
}
