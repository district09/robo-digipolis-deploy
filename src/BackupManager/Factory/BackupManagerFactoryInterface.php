<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Factory;

use BackupManager\Manager;

interface BackupManagerFactoryInterface
{
    /**
     * Creates a backup manager.
     *
     * @param string|array $filesystemConfig
     *   Config for the FilesystemProvider. A path to a PHP file or an array.
     *
     * @param string|array $dbConfig
     *   Config for the DatabaseProvider. A path to a PHP file or an array.
     *
     * @return \BackupManager\Manager
     */
    public static function create($filesystemConfig, $dbConfig): Manager;
}
