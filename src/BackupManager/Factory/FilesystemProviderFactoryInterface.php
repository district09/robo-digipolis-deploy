<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Factory;

interface FilesystemProviderFactoryInterface
{
    /**
     * Creates a FilesystemProvider.
     *
     * @param string|array $filesystemConfig
     *   Config for the FilesystemProvider. A path to a PHP file or an array.
     *
     * @return \District09\BackupManager\Filesystems\FilesystemProvider
     */
    public static function create($filesystemConfig);
}
