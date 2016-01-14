<?php

namespace spec\Deefour\Interactor\Stub\Contexts;

use Deefour\Interactor\Stub\Contexts\MixedContext;
use PhpSpec\ObjectBehavior;

class MixedContextSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beAnInstanceOf(MixedContext::class);
        $this->beConstructedWith('foo', 'bar', ['baz' => 'mmm']);
    }

    public function it_provides_direct_property_access()
    {
        $this->make->shouldBe('foo');
    }

    public function it_still_provides_magic_property_access_on_source()
    {
        $this->baz->shouldBe('mmm');
    }
}
