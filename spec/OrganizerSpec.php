<?php

namespace spec\Deefour\Interactor;

use Mockery;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Deefour\Interactor\Stub\Interactors\RegisterUser;
use Deefour\Interactor\Stub\Contexts\CreateUserContext;
use Deefour\Interactor\Stub\Contexts\CreateVehicleContext;
use Deefour\Interactor\Stub\Contexts\RegisterUserContext;
use Deefour\Interactor\Exception\Failure;
use Deefour\Interactor\Status\Error;
use Deefour\Interactor\Status\Success;

class OrganizerSpec extends ObjectBehavior
{
    function let()
    {
        $a = new CreateUserContext('Jason', 'Daly');
        $b = new CreateVehicleContext('Subaru', 'WRX');

        $context = new RegisterUserContext($a, $b);

        $this->beAnInstanceOf(RegisterUser::class);
        $this->beConstructedWith($context);
    }

    function it_calls_all_interactors() {
        $this->context()->{CreateUserContext::class}->called->shouldBe(false);

        $this->call();

        $this->context()->{CreateUserContext::class}->called->shouldBe(true);
        $this->context()->{CreateVehicleContext::class}->called->shouldBe(true);
    }

    function it_rolls_back_on_failure() {
        $this->context()->{CreateVehicleContext::class}->should_fail = true;

        $this->shouldThrow(Failure::class)->during('call');

        $this->context()->{CreateUserContext::class}->rolled_back->shouldBe(true);
        $this->context()->{CreateVehicleContext::class}->rolled_back->shouldBeNull();
        $this->context()->status()->shouldBeAnInstanceOf(Error::class);

        $this->context()->status()->shouldBeAnInstanceOf(Error::class);
        $this->context()->{CreateUserContext::class}->status()->shouldBeAnInstanceOf(Success::class);
        $this->context()->{CreateVehicleContext::class}->status()->shouldBeAnInstanceOf(Error::class);
    }


}
