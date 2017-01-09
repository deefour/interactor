<?php

namespace spec\Deefour\Interactor\Exception;

use Deefour\Interactor\Context;
use Deefour\Interactor\Exception\Failure;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FailureSpec extends ObjectBehavior
{
    public function let($context)
    {
        $context->beADoubleOf(Context::class);
        $this->beConstructedWith($context);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Failure::class);
    }

    function it_should_provide_method_access_to_context()
    {
        $this->context()->shouldBeAnInstanceOf(Context::class);
    }
}
