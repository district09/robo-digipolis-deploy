<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Factory;

use BackupManager\Config\Config;
use BackupManager\Databases\DatabaseProvider;
use BackupManager\Databases\PostgresqlDatabase;
use DigipolisGent\Robo\Task\Deploy\BackupManager\Databases\MysqlDatabase;

class DatabaseProviderFactory implements DatabaseProviderFactoryInterface
{

    /**
     * {@inheritdoc}
     */
    public static function create($dbConfig)
    {
        $dbConfigObj = is_array($dbConfig)
            ? new Config($dbConfig)
            : Config::fromPhpFile($dbConfig);

        // Add all default databases.
        $databaseProvider = new DatabaseProvider($dbConfigObj);
        $databaseProvider->add(new MysqlDatabase());
        $databaseProvider->add(new PostgresqlDatabase());

        return $databaseProvider;
    }
}
