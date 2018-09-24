<?php

namespace DigipolisGent\Robo\Task\Deploy\Scp\Factory;

use DigipolisGent\Robo\Task\Deploy\Scp\Adapter\ScpPhpseclibAdapter;
use DigipolisGent\Robo\Task\Deploy\Scp\Net\SCP;
use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\AbstractAuth;
use phpseclib\Net\SSH2;

class ScpPhpseclibFactory implements ScpFactoryInterface
{

    /**
     * {@inheritdoc}
     */
    public static function create($host, AbstractAuth $auth, $port = 22, $timeout = 10)
    {
        $ssh = new SSH2($host, $port, $timeout);
        $auth->authenticate($ssh);
        if (!$ssh->isConnected()) {
            throw new \RuntimeException(sprintf(
                "ssh: unable to establish connection to %s on port %s",
                $host,
                $port
            ));
        }
        return new ScpPhpseclibAdapter(new SCP($ssh));
    }
}
