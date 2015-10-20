<?php

namespace spec\Deefour\Interactor\Stub;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Deefour\Interactor\Stub\Interactors\CreateVehicle;

class BasicDispatcherSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Deefour\Interactor\Stub\BasicDispatcher');
    }

    function it_should_instantiate_and_execute_interactors()
    {
        $this->dispatchInteractor(CreateVehicle::class)->called->shouldBe(true);
    }
}
