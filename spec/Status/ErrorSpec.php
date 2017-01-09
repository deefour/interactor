<?php

namespace spec\Deefour\Interactor\Status;

use Deefour\Interactor\Context;
use Deefour\Interactor\Status\Error;
use PhpSpec\ObjectBehavior;

class ErrorSpec extends ObjectBehavior
{
    public function let($context)
    {
        $context->beADoubleOf(Context::class);
        $this->beConstructedWith($context, 'Oops! Something went wrong.');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Error::class);
    }

    public function it_is_ok()
    {
        $this->ok()->shouldBe(false);
    }

    public function it_casts_as_string_to_error_message()
    {
        $this->__toString()->shouldBe('Oops! Something went wrong.');
    }
}
