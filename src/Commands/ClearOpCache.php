<?php

namespace DigipolisGent\Robo\Task\Deploy\Commands;

use DigipolisGent\Robo\Task\Deploy\ClearOpCache as ClearOpCacheTask;

trait ClearOpCache
{

    use \DigipolisGent\Robo\Task\Deploy\Traits\ClearOpCacheTrait;

    /**
     * Command digipolis:database-backup.
     *
     * @param string $environment
     *   The environment.
     * @param array $opts
     *   The command options.
     */
    public function digipolisClearOpCache(
        $environment = ClearOpCacheTask::ENV_FCGI,
        $opts = [
            'host' => null,
        ]
    ) {
        return $this->taskClearOpCache($environment, $opts['host'])->run();
    }
}
