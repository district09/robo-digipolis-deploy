<?php

namespace DigipolisGent\Robo\Task\Deploy\Traits;

use DigipolisGent\Robo\Task\Deploy\DatabaseBackup;

trait DatabaseBackupTrait {

    /**
     * Creates a DatabaseBackup task.
     *
     * @param string|array $filesystemConfig
     *   Config for the FilesystemProvider. A path to a PHP file or an array.
     * @param string|array $dbConfig
     *   Config for the DatabaseProvider. A path to a PHP file or an array.
     *
     * @return \DigipolisGent\Robo\Task\Deploy\DatabaseBackup
     */
    protected function taskDatabaseBackup($filesystemConfig, $dbConfig)
    {
        return $this->task(DatabaseBackup::class, $filesystemConfig, $dbConfig);
    }
}
