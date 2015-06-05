<?php

namespace Deefour\Interactor\Stub\Interactors;

use Deefour\Interactor\Interactor;

class BasicInteractor extends Interactor
{
    public function call($fail = false)
    {
        if ($fail) {
            $this->fail();
        }
    }
}
