<?php

namespace spec\Deefour\Interactor\Stub\Interactors;

use Deefour\Interactor\Context;
use Deefour\Interactor\Status\Error;
use Deefour\Interactor\Stub\Exception\CustomFailure;
use Deefour\Interactor\Stub\Interactors\CreateBlog;
use PhpSpec\ObjectBehavior;

class CreateBlogSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beAnInstanceOf(CreateBlog::class);
        $this->beConstructedWith(new Context);
    }

    public function it_throws_custom_exception_during_failure()
    {
        $this->shouldThrow(CustomFailure::class)->during('call');
        $this->context()->ok()->shouldReturn(false);
        $this->context()->status()->shouldBeAnInstanceOf(Error::class);
        $this->context()->status()->__toString()->shouldReturn('Custom failure message.');
    }
}
