<?php

namespace DigipolisGent\Robo\Task\Deploy\Ssh\Auth;

abstract class AbstractAuth
{

    protected $user;

    abstract public function authenticate($connection);

    public function getUser()
    {
        return $this->user;
    }
}
