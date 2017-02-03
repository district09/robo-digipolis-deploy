<?php

namespace DigipolisGent\Robo\Task\Deploy\Traits;

use DigipolisGent\Robo\Task\Deploy\Scp;
use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\AbstractAuth;

trait ScpTrait {

    /**
     * @param string $host
     *   The host.
     * @param \DigipolisGent\Robo\Task\Deploy\Ssh\Auth\AbstractAuth $auth
     *   Authentication data.
     *
     * @return \DigipolisGent\Robo\Task\Deploy\Scp
     */
    protected function taskScp($host, AbstractAuth $auth)
    {
        return $this->task(Scp::class, $host, $auth);
    }
}
