<?php

namespace DigipolisGent\Robo\Task\Deploy\Commands;

use DigipolisGent\Robo\Task\Deploy\ClearOpCache;

trait ClearOpCache
{

    use \DigipolisGent\Robo\Task\Deploy\Traits\ClearOpCacheTrait;

    /**
     * Command digipolis:database-backup.
     *
     * @param string $database
     *   The database command argument.
     * @param array $opts
     *   The command options.
     */
    public function digipolisClearOpCache(
        $environment = ClearOpCache::ENV_FCGI,
        $opts = [
            'host' => null,
        ]
    )
    {
        $this->taskClearOpCache($environment, $opts['host'])->run();
    }
}
