<?php namespace spec\Deefour\Interactor\Status;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ErrorSpec extends ObjectBehavior {

  function let($context) {
    $context->beADoubleOf('Deefour\Interactor\Context');
    $this->beConstructedWith($context, 'Oops! Something went wrong.');
  }

  function it_is_initializable() {
    $this->shouldHaveType('Deefour\Interactor\Status\Error');
  }

  function it_is_ok() {
    $this->ok()->shouldBe(false);
  }

  function it_casts_as_string_to_error_message() {
    $this->__toString()->shouldBe('Oops! Something went wrong.');
  }

}
