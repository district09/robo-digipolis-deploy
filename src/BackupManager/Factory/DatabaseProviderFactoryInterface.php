<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Factory;

interface DatabaseProviderFactoryInterface
{
    /**
     * Creates a DatabaseProvider.
     *
     * @param string|array $dbConfig
     *   Config for the DatabaseProvider. A path to a PHP file or an array.
     *
     * @return \District09\BackupManager\Databases\DatabaseProvider
     */
    public static function create($dbConfig);
}
