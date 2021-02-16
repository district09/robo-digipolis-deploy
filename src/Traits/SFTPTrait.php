<?php

namespace DigipolisGent\Robo\Task\Deploy\Traits;

use DigipolisGent\Robo\Task\Deploy\SFTP;
use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\AbstractAuth;

trait SFTPTrait
{

    /**
     * @param string $host
     *   The host.
     * @param \DigipolisGent\Robo\Task\Deploy\Ssh\Auth\AbstractAuth $auth
     *   Authentication data.
     *
     * @return \DigipolisGent\Robo\Task\Deploy\SFTP
     */
    protected function taskSFTP($host, AbstractAuth $auth)
    {
        return $this->task(SFTP::class, $host, $auth);
    }
}
