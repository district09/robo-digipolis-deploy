<?php

namespace DigipolisGent\Robo\Task\Deploy\Common;

use Robo\Contract\TaskInterface;

trait DatabaseCommand
{

    protected function defaultFileSystemConfig()
    {
        return [
            'local' => [
                'type' => 'Local',
                'root' => '/',
            ],
        ];
    }

    protected function defaultDbConfig()
    {
        return [
            'default' => [
                'type' => 'mysql',
                'host' => 'localhost',
                'port' => '3306',
                'user' => 'root',
                'pass' => '',
                'database' => dirname(realpath(getcwd())),
            ],
        ];
    }

    /**
     * Apply the database argument and the correct options to the database task.
     * @param string $task
     *   The task method to call.
     * @param string $database
     *   The database argument.
     * @param array $opts
     *   The command options.
     *
     * @return TaskInterface
     *   The task with the arguments and options applied.
     */
    protected function createDbTask($task, $database, $opts)
    {
        $filesystemConfig = $opts['file-system-config']
            ?
            : $this->defaultFileSystemConfig();
        $dbConfig = $opts['database-config']
            ?
            : $this->defaultDbConfig();

        return $this->{$task}($filesystemConfig, $dbConfig)
            ->compression($opts['compression'])
            ->database($database);
    }
}
