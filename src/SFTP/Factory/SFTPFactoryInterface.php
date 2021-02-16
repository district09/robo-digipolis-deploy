<?php

namespace DigipolisGent\Robo\Task\Deploy\SFTP\Factory;

use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\AbstractAuth;

interface SFTPFactoryInterface
{
    /**
     * Creates a SFTPAdapter.
     *
     * @param string $host
     *   The sftp server.
     * @param AbstractAuth $aut
     *   SSH authentication for the server.
     * @param type $port
     * @param type $timeout
     *
     * @return \DigipolisGent\Robo\Task\Deploy\SFTP\Adapter\SFTPAdapterInterface
     */
    public static function create($host, AbstractAuth $auth, $port = 22, $timeout = 10);
}
