<?php

namespace Framework;

use Framework\Auth\User;

interface Auth
{
    /**
     * Undocumented function
     *
     * @return User|null
     */
    public function getUser(): ?User;
}
