<?php

namespace DigipolisGent\Robo\Task\Deploy\SFTP\Factory;

use DigipolisGent\Robo\Task\Deploy\SFTP\Adapter\SFTPPhpseclibAdapter;
use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\AbstractAuth;
use phpseclib3\Net\SFTP;

class SFTPPhpseclibFactory implements SFTPFactoryInterface
{

    /**
     * {@inheritdoc}
     */
    public static function create($host, AbstractAuth $auth, $port = 22, $timeout = 10)
    {
        $sftp = new SFTP($host, $port, $timeout);
        $auth->authenticate($sftp);
        if (!$sftp->isConnected()) {
            throw new \RuntimeException(sprintf(
                "sftp: unable to establish connection to %s on port %s",
                $host,
                $port
            ));
        }
        return new SFTPPhpseclibAdapter($sftp);
    }
}
