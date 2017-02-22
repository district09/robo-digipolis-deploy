<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Factory;

use BackupManager\Manager;
use DigipolisGent\Robo\Task\Deploy\BackupManager\Adapter\BackupManagerAdapter;

class BackupManagerFactory implements BackupManagerFactoryInterface
{

    /**
     * {@inheritdoc}
     */
    public static function create($filesystemConfig, $dbConfig)
    {
        // Add all default filesystems.
        $filesystemProvider = FilesystemProviderFactory::create($filesystemConfig);

        // Add all default databases.
        $databaseProvider = DatabaseProviderFactory::create($dbConfig);

        // Add all default compressors.
        $compressorProvider = CompressorProviderFactory::create();

        return new BackupManagerAdapter(new Manager($filesystemProvider, $databaseProvider, $compressorProvider));
    }
}
