<?php

namespace Deefour\Interactor\Stub\Interactors;

use Deefour\Interactor\Interactor;
use Deefour\Interactor\Stub\Exception\CustomFailure;

class CreateBlog extends Interactor
{
    public function call()
    {
        $this->context()->fail(new CustomFailure);
    }
}
