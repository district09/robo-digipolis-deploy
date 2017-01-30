<?php

namespace DigipolisGent\Robo\Task\Deploy\Ssh\Auth;

use phpseclib\Net\SSH2;

class Password extends AbstractAuth
{
    protected $user;

    protected $password;

    public function __construct($user, $password = null)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function authenticate($connection)
    {
        switch ($connection) {
            case $connection instanceof SSH2:

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
                break;

            case $connection instanceof \Robo\Task\Remote\Ssh:
                throw new \InvalidArgumentException(sprintf(
                    'The Robo ssh task does not support logging in with a password. Use an identityfile instead, in %s.',
                    static::class . '::' . __METHOD__
                ));

            default:
                throw new \InvalidArgumentException(sprintf(
                    'Argument 1 passed to %s must be an instance of \phpseclib\Net\SSH2 or \Robo\Task\Remote\Ssh, %s given.',
                    static::class . '::' . __METHOD__,
                    gettype($connection) == 'object'
                      ? get_class($connection)
                      : gettype($connection)
                ));
        }
    }
}
