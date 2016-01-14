<?php

namespace spec\Deefour\Interactor\Stub;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Deefour\Interactor\Stub\Interactors\CreateVehicle;
use Deefour\Interactor\Stub\Contexts\CreateVehicleContext;

class BasicDispatcherSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Deefour\Interactor\Stub\BasicDispatcher');
    }

    public function it_should_instantiate_and_execute_interactors()
    {
        $this->dispatchInteractor(
            CreateVehicle::class,
            CreateVehicleContext::class,
            [ 'make' => 'Subaru', 'model' => 'WRX']
        )->called->shouldBe(true);
    }
}
