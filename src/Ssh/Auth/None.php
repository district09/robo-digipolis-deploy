<?php

namespace DigipolisGent\Robo\Task\Deploy\Ssh\Auth;

use phpseclib3\Net\SSH2;

class None extends AbstractAuth
{
    public function __construct($user)
    {
        $this->user = $user;
    }

    public function authenticate($connection)
    {
        if (!($connection instanceof SSH2)) {
            throw new \InvalidArgumentException(sprintf(
                'Argument 1 passed to %s must be an instance of \phpseclib3\Net\SSH2, %s given.',
                static::class . '::' . __METHOD__,
                gettype($connection) == 'object'
                  ? get_class($connection)
                  : gettype($connection)
            ));
        }
        if (!$connection->login($this->user)) {
            throw new \RuntimeException(sprintf(
                "fail: unable to authenticate user '%s', using password: NO",
                $this->user
            ));
        }
    }
}
