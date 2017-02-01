<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Factory;

interface StorageFactoryInterface
{
    /**
     * Creates a FilesystemProvider.
     *
     * @param string|array $storageConfig
     *   Config for the FilesystemProvider. A path to a PHP file or an array.
     *
     * @return \BackupManager\Filesystems\FilesystemProvider
     */
    public static function create($storageConfig);
}
