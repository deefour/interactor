<?php

namespace Deefour\Interactor\Stub\Models;

class User
{
    protected $attributes;

    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    public function __get($property)
    {
        if (isset($this->attributes[$property])) {
            return $this->attributes[$property];
        }

        return null;
    }
}
