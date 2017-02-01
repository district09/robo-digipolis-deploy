<?php

namespace DigipolisGent\Robo\Task\Deploy\Ssh\Auth;

abstract class AbstractAuth
{
    abstract public function authenticate($connection);
}
