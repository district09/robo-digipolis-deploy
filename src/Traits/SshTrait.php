<?php

namespace DigipolisGent\Robo\Task\Deploy\Traits;

use DigipolisGent\Robo\Task\Deploy\Ssh;
use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\AbstractAuth;

trait SshTrait {

    /**
     * @param string $host
     *   The host.
     * @param \DigipolisGent\Robo\Task\Deploy\Ssh\Auth\AbstractAuth $auth
     *   Authentication data.
     *
     * @return \DigipolisGent\Robo\Task\Deploy\Ssh
     */
    protected function taskSsh($host, AbstractAuth $auth)
    {
        return $this->task(Ssh::class, $host, $auth);
    }
}
