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

    protected function defaultDbConfig($drupal = false)
    {
        $dbConfig = [
            'default' => [
                'type' => 'mysql',
                'host' => 'localhost',
                'port' => '3306',
                'user' => 'root',
                'pass' => '',
                'database' => dirname(realpath(getcwd())),
            ],
        ];
        return $drupal
            ? $this->parseDrupalDbConfig()
            : $dbConfig;
    }

    protected function parseDrupalDbConfig()
    {
        $webDir = $this->getConfig()->get('digipolis.project.web', false);
        if (!$webDir) {
            return false;
        }

        $finder = new \Symfony\Component\Finder\Finder();
        $finder->in($webDir . '/sites')->files()->name('settings.php');
        foreach ($finder as $settingsFile) {
            include_once $settingsFile->getRealpath();
            break;
        }
        if (!isset($databases['default']['default'])) {
            return false;
        }
        $config = $databases['default']['default'];
        return [
          'default' => [
                'type' => $config['driver'],
                'host' => $config['host'],
                'port' => isset($config['port']) ? $config['port'] : '3306',
                'user' => $config['username'],
                'pass' => $config['pass'],
                'database' => $config['database'],
                'structureTables' => [
                    'batch',
                    'cache',
                    'cache_*',
                    '*_cache',
                    '*_cache_*',
                    'flood',
                    'search_dataset',
                    'search_index',
                    'search_total',
                    'semaphore',
                    'sessions',
                    'watchdog',
                ],
            ]
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
            : $this->defaultDbConfig($opts['drupal']);

        return $this->{$task}($filesystemConfig, $dbConfig)
            ->compression($opts['comporession'])
            ->database($database);
    }
}
