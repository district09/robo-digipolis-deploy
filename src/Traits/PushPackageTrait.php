<?php

namespace DigipolisGent\Robo\Task\Deploy\Traits;

use DigipolisGent\Robo\Task\Deploy\PushPackage;
use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\AbstractAuth;

trait PushPackageTrait
{

    /**
     * @param string $host
     *   The host.
     * @param \DigipolisGent\Robo\Task\Deploy\Ssh\Auth\AbstractAuth $auth
     *   Authentication data.
     *
     * @return \DigipolisGent\Robo\Task\Deploy\PushPackage
     */
    protected function taskPushPackage($host, AbstractAuth $auth)
    {
        return $this->task(PushPackage::class, $host, $auth);
    }
}
