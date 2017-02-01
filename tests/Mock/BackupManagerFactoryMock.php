<?php

namespace DigipolisGent\Tests\Robo\Task\Deploy\Mock;

use DigipolisGent\Robo\Task\Deploy\BackupManager\Factory\BackupManagerFactoryInterface;

class BackupManagerFactoryMock implements BackupManagerFactoryInterface
{
    protected static $storageConfig;
    protected static $dbConfig;
    protected static $mock;
    /**
     * {@inheritdoc}
     */
    public static function create($storageConfig, $dbConfig)
    {
        if ($storageConfig != static::$storageConfig || $dbConfig != static::$dbConfig) {
            throw new \Exception('Factory called with invalid arguments');
        }
        return static::$mock;
    }

    public static function setMock($mock)
    {
        static::$mock = $mock;
    }

    static function setStorageConfig($storageConfig)
    {
        static::$storageConfig = $storageConfig;
    }

    static function setDbConfig($dbConfig)
    {
        static::$dbConfig = $dbConfig;
    }
}
