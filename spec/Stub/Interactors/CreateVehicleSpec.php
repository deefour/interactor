<?php

namespace spec\Deefour\Interactor\Stub\Interactors;

use Deefour\Interactor\Stub\Contexts\CreateVehicleContext;
use Deefour\Interactor\Stub\Interactors\CreateVehicle;
use PhpSpec\ObjectBehavior;

class BasicInteractorSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beAnInstanceOf(CreateVehicle::class);
        $this->beConstructedWith(new CreateVehicleContext('Subaru', 'WRX'));
    }

    public function it_accepts_message_on_fail_convenience_method()
    {
        $this->call();
        $this->context()->status()->__toString()->shouldReturn('This is a failure');
    }
}


