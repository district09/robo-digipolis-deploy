<?php

namespace DigipolisGent\Robo\Task\Deploy\Traits;

use DigipolisGent\Robo\Task\Deploy\ClearOpCache;

trait ClearOpCacheTrait
{

    /**
     * Creates a new ClearOpCache task.
     *
     * @param string $environment
     *   One of the ClearOpCache::ENV_* constants.
     * @param string $host
     *   If the environment is FCGI, the host (path to socket or ip:port).
     *
     * @return \DigipolisGent\Robo\Task\Package\Deploy\ClearOpCache
     *   The clear opcache task.
     */
    protected function taskClearOpCache($environment = ClearOpCache::ENV_FCGI, $host = null)
    {
        return $this->task(ClearOpCache::class, $environment, $host);
    }
}
