<?php

namespace Deefour\Interactor\Stub\Contexts;

use Deefour\Interactor\Context;

class PlainContext extends Context
{
  public function baz()
  {
      return true;
  }
}
