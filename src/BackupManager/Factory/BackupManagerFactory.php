<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Factory;

use BackupManager\Compressors\CompressorProvider;
use BackupManager\Compressors\GzipCompressor;
use BackupManager\Compressors\NullCompressor;
use BackupManager\Config\Config;
use BackupManager\Databases\DatabaseProvider;
use BackupManager\Databases\PostgresqlDatabase;
use BackupManager\Filesystems\Awss3Filesystem;
use BackupManager\Filesystems\DropboxFilesystem;
use BackupManager\Filesystems\FilesystemProvider;
use BackupManager\Filesystems\FtpFilesystem;
use BackupManager\Filesystems\GcsFilesystem;
use BackupManager\Filesystems\LocalFilesystem;
use BackupManager\Filesystems\RackspaceFilesystem;
use BackupManager\Filesystems\SftpFilesystem;
use BackupManager\Manager;
use DigipolisGent\Robo\Task\Deploy\BackupManager\Compressors\TarCompressor;
use DigipolisGent\Robo\Task\Deploy\BackupManager\Databases\MysqlDatabase;

class BackupManagerFactory implements BackupManagerFactoryInterface
{
  /**
   * {@inheritdoc}
   */
  public static function create($storageConfig, $dbConfig)
  {
      $storageConfigObj = is_array($storageConfig)
          ? new Config($storageConfig)
          : Config::fromPhpFile($storageConfig);

      $dbConfigObj = is_array($dbConfig)
          ? new Config($dbConfig)
          : Config::fromPhpFile($dbConfig);

      $filesystemProvider = new FilesystemProvider($storageConfigObj);

      // Add all default filesystems.
      $filesystemProvider->add(new Awss3Filesystem());
      $filesystemProvider->add(new GcsFilesystem());
      $filesystemProvider->add(new DropboxFilesystem());
      $filesystemProvider->add(new FtpFilesystem());
      $filesystemProvider->add(new LocalFilesystem());
      $filesystemProvider->add(new RackspaceFilesystem());
      $filesystemProvider->add(new SftpFilesystem());

      // Add all default databases.
      $databaseProvider = new DatabaseProvider($dbConfigObj);
      $databaseProvider->add(new MysqlDatabase());
      $databaseProvider->add(new PostgresqlDatabase());

      // Add all default compressors.
      $compressorProvider = new CompressorProvider();
      $compressorProvider->add(new TarCompressor());
      $compressorProvider->add(new GzipCompressor());
      $compressorProvider->add(new NullCompressor());

      return new Manager($filesystemProvider, $databaseProvider, $compressorProvider);
  }
}
