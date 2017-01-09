<?php

namespace Deefour\Interactor\Stub\Interactors;

use Deefour\Interactor\Organizer;
use Deefour\Interactor\Stub\Contexts\CreateUserContext;
use Deefour\Interactor\Stub\Contexts\CreateVehicleContext;
use Deefour\Interactor\Stub\Contexts\RegisterUserContext;

class RegisterUser extends Organizer
{
    /**
     * Constructor.
     *
     * @param RegisterUserContext $context A composite context for the organizer.
     */
    public function __construct(RegisterUserContext $context)
    {
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     *
     * Create the new user and their first vehicle.
     */
    public function organize()
    {
        $this->addInteractor(new CreateUser($this->getContext(CreateUserContext::class)));
        $this->addInteractor(new CreateVehicle($this->getContext(CreateVehicleContext::class)));
    }
}
