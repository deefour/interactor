<?php namespace spec\Deefour\Interactor\Status;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SuccessSpec extends ObjectBehavior {

  function let($context) {
    $context->beADoubleOf('Deefour\Interactor\Context');
    $this->beConstructedWith($context);
  }

  function it_is_initializable() {
    $this->shouldHaveType('Deefour\Interactor\Status\Success');
  }

  function it_is_ok() {
    $this->ok()->shouldBe(true);
  }

  function it_casts_as_string_to_ok() {
    $this->__toString()->shouldBe('OK');
  }

}
