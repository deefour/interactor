<?php

namespace spec\Deefour\Interactor;

use Deefour\Interactor\Stub\Contexts\CreateUserContext;
use Deefour\Interactor\Stub\Contexts\CreateVehicleContext;
use Deefour\Interactor\Stub\Contexts\RegisterUserContext;
use PhpSpec\ObjectBehavior;

class CompositeContextSpec extends ObjectBehavior
{
    public function let()
    {
        $a = new CreateUserContext('Jason', 'Daly');
        $b = new CreateVehicleContext('Subaru', 'WRX');

        $this->beAnInstanceOf(RegisterUserContext::class);
        $this->beConstructedWith($a, $b);
    }

    public function it_provides_access_to_underlying_contexts_via_fqcn()
    {
        $this->get(CreateUserContext::class)->shouldBeAnInstanceOf(CreateUserContext::class);
        $this->get(CreateUserContext::class)->firstName->shouldBe('Jason');
        $this->get(CreateVehicleContext::class)->model->shouldBe('WRX');
    }
}
