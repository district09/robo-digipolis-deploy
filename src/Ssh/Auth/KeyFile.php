<?php

namespace DigipolisGent\Robo\Task\Deploy\Ssh\Auth;

use phpseclib\Net\SSH2;
use phpseclib\Crypt\RSA;

class KeyFile extends AbstractAuth
{
    protected $user;

    protected $publicKeyFile;

    protected $privateKeyFile;

    protected $passphrase;

    public function __construct($user, $privateKeyFile, $passphrase = null)
    {
        $this->user = $user;
        $this->privateKeyFile = $privateKeyFile;
        $this->publicKeyFile = $privateKeyFile . '.pub';
        $this->passphrase = $passphrase;
    }

    public function authenticate($connection)
    {
        switch ($connection) {
            case $connection instanceof SSH2:
                $rsa = new RSA();
                $rsa->loadKey(file_get_contents($this->privateKeyFile));
                $rsa->setPublicKey(file_get_contents($this->publicKeyFile));
                if (!is_null($this->passphrase)) {
                    $rsa->setPassword($this->passphrase);
                }
                if (!$connection->login($this->user, $rsa)) {
                    throw new \RuntimeException(sprintf(
                        "fail: unable to authenticate user '%s' using key file",
                        $this->user
                    ));
                }
                break;

            case $connection instanceof \Robo\Task\Remote\Ssh:
                $connection->identityFile($this->privateKeyFile);
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
