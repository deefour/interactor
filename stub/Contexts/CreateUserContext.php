<?php

namespace Deefour\Interactor\Stub\Contexts;

use Deefour\Interactor\Context;

class CreateUserContext extends Context
{
    public function __construct($first_name, $last_name)
    {
        parent::__construct(
            array_merge(get_defined_vars(), [ 'called' => false ])
        );
    }
}
