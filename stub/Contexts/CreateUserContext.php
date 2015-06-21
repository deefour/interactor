<?php

namespace Deefour\Interactor\Stub\Contexts;

use Deefour\Interactor\Context;

class CreateUserContext extends Context
{
    public $firstName;

    public $lastName;

    public function __construct($firstName, $lastName)
    {
        $this->firstName = $firstName;
        $this->lastName  = $lastName;

        parent::__construct([ 'called' => false ]);
    }
}
