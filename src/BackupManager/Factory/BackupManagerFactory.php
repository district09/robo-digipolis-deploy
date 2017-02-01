<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Factory;

use BackupManager\Manager;

/**
 * @todo: split up in factories for filesystem & database.
 */
class BackupManagerFactory implements BackupManagerFactoryInterface
{

    /**
     * {@inheritdoc}
     */
    public static function create($storageConfig, $dbConfig)
    {
        // Add all default filesystems.
        $filesystemProvider = StorageFactory::create($storageConfig);

        // Add all default databases.
        $databaseProvider = DatabaseFactory::create($dbConfig);

        // Add all default compressors.
        $compressorProvider = CompressorFactory::create();

        return new Manager($filesystemProvider, $databaseProvider, $compressorProvider);
    }
}
