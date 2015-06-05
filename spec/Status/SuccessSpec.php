<?php

namespace spec\Deefour\Interactor\Status;

use PhpSpec\ObjectBehavior;

class SuccessSpec extends ObjectBehavior
{
  public function let($context)
  {
      $context->beADoubleOf('Deefour\Interactor\Context');
      $this->beConstructedWith($context);
  }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Deefour\Interactor\Status\Success');
    }

    public function it_is_ok()
    {
        $this->ok()->shouldBe(true);
    }

    public function it_casts_as_string_to_ok()
    {
        $this->__toString()->shouldBe('OK');
    }
}
