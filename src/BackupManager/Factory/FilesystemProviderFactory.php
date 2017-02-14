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
