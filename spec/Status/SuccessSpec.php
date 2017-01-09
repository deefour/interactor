<?php

namespace spec\Deefour\Interactor\Status;

use Deefour\Interactor\Context;
use Deefour\Interactor\Status\Success;
use PhpSpec\ObjectBehavior;

class SuccessSpec extends ObjectBehavior
{
    public function let($context)
    {
        $context->beADoubleOf(Context::class);
        $this->beConstructedWith($context);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Success::class);
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
