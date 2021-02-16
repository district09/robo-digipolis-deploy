<?php

namespace DigipolisGent\Robo\Task\Deploy\Ssh\Auth;

use phpseclib3\Net\SSH2;

class Password extends AbstractAuth
{
    protected $password;

    public function __construct($user, $password = null)
    {
        $this->user = $user;
        $this->password = $password;
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
        if ($this->password === null) {
            $authenticator = new None($this->user);
            $authenticator->authenticate($connection);
            return;
        }
        if (!$connection->login($this->user, $this->password)) {
            throw new \RuntimeException(sprintf(
                "fail: unable to authenticate user '%s', using password: YES",
                $this->user
            ));
        }
    }
}
