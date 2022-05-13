<?php

namespace DigipolisGent\Robo\Task\Deploy\Common;

use Consolidation\AnnotatedCommand\Events\CustomEventAwareInterface;

trait DatabaseCommand
{
    protected $fileSystemConfig;

    protected $dbConfig;

    public function setFileSystemConfig($fileSystemConfig)
    {
        $this->fileSystemConfig = $fileSystemConfig;
    }

    public function setDbConfig($dbConfig)
    {
        $this->dbConfig = $dbConfig;
    }


    protected function defaultFileSystemConfig()
    {
        return $this->fileSystemConfig ?? [
            'local' => [
                'type' => 'Local',
                'root' => '/',
            ],
        ];
    }

    protected function defaultDbConfig()
    {
        return $this->dbConfig ?? [
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
     *
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
        if ($this instanceof CustomEventAwareInterface) {
            foreach ($this->getCustomEventHandlers('digipolis-db-config') as $handler) {
                $dbConfig = $handler($this);
                $this->setDbConfig($dbConfig);
            }
        }
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
