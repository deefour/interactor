<?php

namespace Deefour\Interactor\Stub\Contexts;

use Deefour\Interactor\CompositeContext;

class RegisterUserContext extends CompositeContext {
    /**
     * Constructor.
     *
     * {@inheritdoc}
     *
     * @param CreateUserContext    $createUser
     * @param CreateVehicleContext $createVehicle
     */
    public function __construct(
        CreateUserContext $createUser,
        CreateVehicleContext $createVehicle
    ) {
        parent::__construct(func_get_args());
    }
}
