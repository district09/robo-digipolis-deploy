<?php

namespace DigipolisGent\Robo\Task\Deploy\Ssh\Auth;

use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;

class KeyFile extends AbstractAuth
{
    protected $privateKeyFile;

    protected $passphrase;

    public function __construct($user, $privateKeyFile, $passphrase = null)
    {
        $this->user = $user;
        $this->privateKeyFile = $privateKeyFile;
        $this->passphrase = $passphrase;
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
        $key = PublicKeyLoader::load(file_get_contents($this->privateKeyFile), is_null($this->passphrase) ? false : $this->passphrase);
        if (!$connection->login($this->user, $key)) {
            throw new \RuntimeException(sprintf(
                "Failed: unable to authenticate user '%s' using key file '%s'. Log:\n",
                $this->user,
                $this->privateKeyFile,
                $connection->getLog()
            ));
        }
    }
}
