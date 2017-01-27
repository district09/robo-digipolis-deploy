<?php

namespace DigipolisGent\Robo\Task\Deploy\Scp\Factory;

use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\AbstractAuth;

interface ScpFactoryInterface
{
    /**
     * Creates a ScpAdapter.
     *
     * @param string $host
     *   The scp server.
     * @param AbstractAuth $aut
     *   SSH authentication for the server.
     * @param type $port
     * @param type $timeout
     *
     * @return \DigipolisGent\Robo\Task\Deploy\Scp\Adapter\ScpAdapterInterface
     */
    public static function create($host, AbstractAuth $auth, $port = 22, $timeout = 10);
}
