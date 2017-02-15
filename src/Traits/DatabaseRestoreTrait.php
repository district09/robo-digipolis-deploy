<?php

namespace DigipolisGent\Robo\Task\Deploy\Traits;

use DigipolisGent\Robo\Task\Deploy\DatabaseRestore;

trait DatabaseRestoreTrait
{

    /**
     * Creates a DatabaseRestore task.
     *
     * @param string|array $filesystemConfig
     *   Config for the FilesystemProvider. A path to a PHP file or an array.
     * @param string|array $dbConfig
     *   Config for the DatabaseProvider. A path to a PHP file or an array.
     *
     * @return \DigipolisGent\Robo\Task\Deploy\DatabaseRestore
     */
    protected function taskDatabaseRestore($filesystemConfig, $dbConfig)
    {
        return $this->task(DatabaseRestore::class, $filesystemConfig, $dbConfig);
    }
}
