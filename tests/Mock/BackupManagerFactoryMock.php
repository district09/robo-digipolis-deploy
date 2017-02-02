<?php

namespace DigipolisGent\Tests\Robo\Task\Deploy\Mock;

use DigipolisGent\Robo\Task\Deploy\BackupManager\Factory\BackupManagerFactoryInterface;

class BackupManagerFactoryMock implements BackupManagerFactoryInterface
{
    protected static $filesystemConfig;
    protected static $dbConfig;
    protected static $mock;
    /**
     * {@inheritdoc}
     */
    public static function create($filesystemConfig, $dbConfig)
    {
        if ($filesystemConfig != static::$filesystemConfig || $dbConfig != static::$dbConfig) {
            throw new \Exception('Factory called with invalid arguments');
        }
        return static::$mock;
    }

    public static function setMock($mock)
    {
        static::$mock = $mock;
    }

    static function setFilesystemConfig($filesystemConfig)
    {
        static::$filesystemConfig = $filesystemConfig;
    }

    static function setDbConfig($dbConfig)
    {
        static::$dbConfig = $dbConfig;
    }
}
