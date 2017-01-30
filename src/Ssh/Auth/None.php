<?php

namespace DigipolisGent\Robo\Task\Deploy\Ssh\Auth;

use phpseclib\Net\SSH2;

class None extends AbstractAuth
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function authenticate($connection)
    {
        switch ($connection) {
            case $connection instanceof SSH2:
                if (!$connection->login($this->user)) {
                    throw new \RuntimeException(sprintf(
                        "fail: unable to authenticate user '%s', using password: NO",
                        $this->user
                    ));
                }
                break;

            case $connection instanceof \Robo\Task\Remote\Ssh:
                $connection->user($this->user);
                break;

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
