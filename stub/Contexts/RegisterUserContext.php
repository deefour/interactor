<?php

namespace Deefour\Interactor\Stub\Contexts;

use Deefour\Interactor\Context;

class RegisterUserContext extends Context
{
    /**
     * Constructor.
     *
     * @param array $user
     * @param array $vin
     */
    public function __construct(array $user, $vin)
    {
        parent::__construct(compact('user', 'vin'));
    }
}
