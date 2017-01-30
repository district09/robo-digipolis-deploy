<?php

namespace DigipolisGent\Tests\Robo\Task\Deploy\Mock;

use DigipolisGent\Robo\Task\Deploy\Ssh\Factory\SshFactoryInterface;
use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\AbstractAuth;

class SshFactoryMock implements SshFactoryInterface
{
    protected static $mock;
    protected static $host;
    protected static $auth;
    protected static $port;
    protected static $timeout;

    /**
     * {@inheritdoc}
     */
    public static function create($host, $port = 22, $timeout = 10)
    {
        if ($host !== static::$host || $port !== static::$port || $timeout !== static::$timeout) {
            throw new \Exception('Factory called with invalid arguments');
        }
        return static::$mock;
    }

    public static function setMock($mock)
    {
        static::$mock = $mock;
    }

    static function setHost($host)
    {
        static::$host = $host;
    }

    static function setPort($port)
    {
        static::$port = $port;
    }

    static function setTimeout($timeout)
    {
        static::$timeout = $timeout;
    }
}
