<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Factory;

interface BackupManagerFactoryInterface
{
    /**
     * Creates a BackupManagerAdapter.
     *
     * @param string|array $storageConfig
     *   Config for the FilesystemProvider. A path to a PHP file or an array.
     *
     * @param string|array $dbConfig
     *   Config for the DatabaseProvider. A path to a PHP file or an array.
     *
     * @return \DigipolisGent\Robo\Task\Deploy\BackupManager\Adapter\BackupManagerAdapterInterface
     */
    public static function create($storageConfig, $dbConfig);
}
