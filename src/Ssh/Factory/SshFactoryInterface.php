<?php

namespace DigipolisGent\Robo\Task\Deploy\Ssh\Factory;

interface SshFactoryInterface
{
    /**
     * Creates a SshAdapter.
     *
     * @param string $host
     *   The ssh server.
     * @param type $port
     * @param type $timeout
     *
     * @return \DigipolisGent\Robo\Task\Deploy\Ssh\Adapter\SshAdapterInterface
     */
    public static function create($host, $port = 22, $timeout = 10);
}
