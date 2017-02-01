<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Factory;

use BackupManager\Config\Config;
use BackupManager\Filesystems\Awss3Filesystem;
use BackupManager\Filesystems\DropboxFilesystem;
use BackupManager\Filesystems\FilesystemProvider;
use BackupManager\Filesystems\FtpFilesystem;
use BackupManager\Filesystems\GcsFilesystem;
use BackupManager\Filesystems\LocalFilesystem;
use BackupManager\Filesystems\RackspaceFilesystem;
use BackupManager\Filesystems\SftpFilesystem;

class StorageFactory implements StorageFactoryInterface
{

    /**
     * {@inheritdoc}
     */
    public static function create($storageConfig)
    {
        $storageConfigObj = is_array($storageConfig)
            ? new Config($storageConfig)
            : Config::fromPhpFile($storageConfig);

        $filesystemProvider = new FilesystemProvider($storageConfigObj);

        // Add all default filesystems.
        $filesystemProvider->add(new Awss3Filesystem());
        $filesystemProvider->add(new GcsFilesystem());
        $filesystemProvider->add(new DropboxFilesystem());
        $filesystemProvider->add(new FtpFilesystem());
        $filesystemProvider->add(new LocalFilesystem());
        $filesystemProvider->add(new RackspaceFilesystem());
        $filesystemProvider->add(new SftpFilesystem());

        return $filesystemProvider;
    }
}
