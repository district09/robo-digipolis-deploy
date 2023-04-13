<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Factory;

use District09\BackupManager\Config\Config;
use District09\BackupManager\Filesystems\Awss3Filesystem;
use District09\BackupManager\Filesystems\DropboxFilesystem;
use District09\BackupManager\Filesystems\FilesystemProvider;
use District09\BackupManager\Filesystems\FtpFilesystem;
use District09\BackupManager\Filesystems\GcsFilesystem;
use District09\BackupManager\Filesystems\LocalFilesystem;
use District09\BackupManager\Filesystems\RackspaceFilesystem;
use District09\BackupManager\Filesystems\SftpFilesystem;

class FilesystemProviderFactory implements FilesystemProviderFactoryInterface
{

    /**
     * {@inheritdoc}
     */
    public static function create($filesystemConfig)
    {
        $filesystemConfigObj = is_array($filesystemConfig)
            ? new Config($filesystemConfig)
            : Config::fromPhpFile($filesystemConfig);

        $filesystemProvider = new FilesystemProvider($filesystemConfigObj);

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
