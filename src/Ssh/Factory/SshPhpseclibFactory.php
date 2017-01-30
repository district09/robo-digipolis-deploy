<?php

namespace DigipolisGent\Robo\Task\Deploy\Ssh\Factory;

use DigipolisGent\Robo\Task\Deploy\Ssh\Adapter\SshPhpseclibAdapter;

class SshPhpseclibFactory implements SshFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public static function create($host, $port = 22, $timeout = 10)
    {
        return new SshPhpseclibAdapter($host, $port, $timeout);
    }
}
