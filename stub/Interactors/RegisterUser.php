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
     * Create the new user and their first vehicle.
     */
    public function organize()
    {
        $this->enqueue(function ($context) {
            return new CreateUser(
                new CreateUserContext($context->user['first_name'], $context->user['last_name'])
            );
        });

        $this->enqueue(function ($context, $previous) {
            return new CreateVehicle(
                new CreateVehicleContext($previous->user, $context->vin)
            );
        });
    }
}
