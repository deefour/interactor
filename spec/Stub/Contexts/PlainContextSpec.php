<?php

namespace spec\Deefour\Interactor\Stub\Contexts;

use Deefour\Interactor\Exception\Failure;
use Deefour\Interactor\Status\Error;
use Deefour\Interactor\Status\Success;
use Deefour\Interactor\Stub\Contexts\PlainContext;
use PhpSpec\ObjectBehavior;

class PlainContextSpec extends ObjectBehavior
{
    protected $source = ['foo' => true, 'bar' => false];

    public function let()
    {
        $this->beAnInstanceOf(PlainContext::class);
        $this->beConstructedWith($this->source);
    }

    public function it_has_successful_status_by_default()
    {
        $this->status()->shouldBeAnInstanceOf(Success::class);
    }

    public function it_is_ok_by_default()
    {
        $this->ok()->shouldReturn(true);
    }

    public function it_throws_failure_and_changes_status_on_failure()
    {
        $this->shouldThrow(Failure::class)->duringFail('BAD ERROR');
        $this->status()->shouldBeAnInstanceOf(Error::class);
    }

    public function it_allows_failure_without_message()
    {
        $this->shouldThrow(Failure::class)->duringFail();
    }

    public function it_is_not_ok_after_failure()
    {
        $this->shouldThrow(Failure::class)->duringFail();

        $this->ok()->shouldReturn(false);
    }

    public function it_provides_magic_getter_access_to_source()
    {
        $this->foo->shouldBe(true);
        $this->bar->shouldBe(false);
        $this->unknownProperty->shouldBeNull();
    }

    public function it_provide_magic_getter_access_to_public_methods()
    {
        $this->baz->shouldBe(true);
    }

    public function it_provides_access_to_underlying_transformer()
    {
        $this->raw()->shouldReturn($this->source);
        $this->raw('foo')->shouldBe(true);
        $this->get('bar')->shouldBe(false);
    }

    public function it_allows_mutation_of_underlying_source()
    {
        $this->foo = 'omg!';

        $this->raw('foo')->shouldBe('omg!');
    }

    public function it_should_print_status_message_when_cast_to_string()
    {
        $this->__toString()->shouldBe('OK');
    }
}
