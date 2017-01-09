<?php

namespace spec\Deefour\Interactor\Stub;

use Deefour\Interactor\Stub\Contexts\CreateVehicleContext;
use Deefour\Interactor\Stub\Interactors\CreateVehicle;
use Deefour\Interactor\Stub\Models\User;
use PhpSpec\ObjectBehavior;

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
            [ 'user' => new User('Jason', 'Daly'), 'vin' => 'VINNUMBERHERE' ]
        )->called->shouldBe(true);
    }
}
