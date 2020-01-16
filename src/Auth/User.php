<?php

namespace App\Auth;

use Framework\Auth\User as AuthUser;

class User implements AuthUser
{
    public $username;
    public $email;
    public $id;
    public $password;

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getRoles(): array
    {
        return [];
    }
}
