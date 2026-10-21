<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;

class SimpleUser implements Authenticatable
{
    public function __construct(
        protected string $username
    ) {}

    public function getAuthIdentifierName()
    {
        return 'username';
    }

    public function getAuthIdentifier()
    {
        return $this->username;
    }

    public function getAuthPasswordName()
    {
        return null;
    }

    public function getAuthPassword()
    {
        return null;
    }

    public function getRememberToken()
    {
        return '';
    }

    public function setRememberToken($value)
    {
        //
    }

    public function getRememberTokenName()
    {
        return '';
    }
}
