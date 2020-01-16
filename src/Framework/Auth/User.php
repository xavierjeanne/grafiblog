<?php

namespace Framework\Auth;

interface User
{
    public function getUsername(): string;

    public function getRoles(): array;
}
