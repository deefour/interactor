<?php

namespace spec\Deefour\Interactor;

use Deefour\Interactor\Exception\Failure;
use Deefour\Interactor\Status\Error;
use Deefour\Interactor\Stub\Contexts\RegisterUserContext;
use Deefour\Interactor\Stub\Interactors\RegisterUser;
use PhpSpec\ObjectBehavior;

class OrganizerSpec extends ObjectBehavior
{
    public function let()
    {
        $context = new RegisterUserContext(
            [ 'first_name' => 'Jason', 'last_name' => 'Daly'], 'VINNUMBERHERE'
        );

        $this->beAnInstanceOf(RegisterUser::class);
        $this->beConstructedWith($context);
    }

    public function it_calls_all_interactors()
    {
        $this->completed()->shouldHaveCount(0);

        $this->call();

        $this->completed()->shouldHaveCount(2);

        foreach ($this->completed() as $interactor) {
            $interactor->context()->called->shouldBe(true);
        }
    }

    public function it_rolls_back_on_failure()
    {
        $context = new RegisterUserContext(
            [ 'first_name' => 'Jason', 'last_name' => 'Daly'], 'invalid-format'
        );

        $this->beConstructedWith($context);

        $this->completed()->shouldHaveCount(0);

        $this->shouldThrow(Failure::class)->during('call');

        $this->completed()->shouldHaveCount(1);

        foreach ($this->completed() as $interactor) {
            $interactor->context()->rolled_back->shouldBe(true);
        }

        $this->context()->status()->shouldBeAnInstanceOf(Error::class);
    }
}
