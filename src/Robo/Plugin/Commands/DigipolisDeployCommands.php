<?php

namespace DigipolisGent\Robo\Task\Deploy\Robo\Plugin\Commands;

use DigipolisGent\Robo\Task\Deploy\ClearOpCache as ClearOpCacheTask;
use DigipolisGent\Robo\Task\Deploy\PartialCleanDirs as PartialCleanDirsTask;
use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\KeyFile;
use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\Password;
use DigipolisGent\Robo\Task\General\Common\DigipolisPropertiesAwareInterface;
use Robo\Symfony\ConsoleIO;

class DigipolisDeployCommands extends \Robo\Tasks implements DigipolisPropertiesAwareInterface, \Robo\Contract\ConfigAwareInterface
{
    use \DigipolisGent\Robo\Task\Deploy\Common\DatabaseCommand;
    use \DigipolisGent\Robo\Task\Deploy\Tasks;
    use \DigipolisGent\Robo\Task\General\Common\DigipolisPropertiesAware;
    use \Consolidation\Config\ConfigAwareTrait;

    /**
     * @command digipolis:clear-op-cache
     */
    public function digipolisClearOpCache(
        ConsoleIO $io,
        $environment = ClearOpCacheTask::ENV_FCGI,
        $opts = [
            'host' => null,
        ]
    ) {
        return $this->taskClearOpCache($environment, $opts['host'])->run();
    }

    /**
     * @command digipolis:database-backup
     */
    public function digipolisDatabaseBackup(
        $database = 'default',
        $opts = [
            'file-system-config|fsconf' => null,
            'database-config|dbconf' => null,
            'compression|c' => 'gzip',
            'destination|d' => null,
            'destination-type|dtype' => 'local',
        ]
    ) {
        $this->readProperties();
        $destination = $opts['destination']
            ? $opts['destination']
            : realpath(getcwd()) . '/project.tar.gz';
        if (!$opts['file-system-config']) {
            $opts['file-system-config'] = [
                $opts['destination-type'] => [
                    'type' => ucfirst($opts['destination-type']),
                    'root' => realpath(dirname($destination)),
                ],
            ];
            $destination = basename($destination);
        }
        return $this->createDbTask('taskDatabaseBackup', $database, $opts)
            ->destination($destination, $opts['destination-type'])
            ->run();
    }

    /**
     * @command digipolis:database-restore
     */
    public function digipolisDatabaseRestore($database = 'default', $opts = [
      'file-system-config|fsconf' => null,
      'database-config|dbconf' => null,
      'compression|c' => 'gzip',
      'source|s' => null,
      'source-type|stype' => 'local',
    ])
    {
        $this->readProperties();
        $source = $opts['source']
            ? $opts['source']
            : realpath(getcwd()) . '/project.tar.gz';
        if (!$opts['file-system-config']) {
            $opts['file-system-config'] = [
                $opts['source-type'] => [
                    'type' => ucfirst($opts['source-type']),
                    'root' => realpath(dirname($source)),
                ],
            ];
            $source = basename($source);
        }
        return $this->createDbTask('taskDatabaseRestore', $database, $opts)
            ->source($source, $opts['source-type'])
            ->run();
    }

    /**
     * @command digipolis:clean-dir
     */
    public function digipolisCleanDir($dirs, $opts = ['sort' => PartialCleanDirsTask::SORT_NAME])
    {
        $dirsArg = array();
        foreach (array_map('trim', explode(',', $dirs)) as $dir) {
            $dirParts = explode(':', $dir);
            if (count($dirParts) > 1) {
                $dirsArg[$dirParts[0]] = $dirParts[1];
                continue;
            }
            $dirsArg[] = $dirParts[0];
        }
        return $this->taskPartialCleanDirs($dirsArg)
            ->sortBy($opts['sort'])
            ->run();
    }

    /**
     * @command digipolis:push-package
     */
    public function digipolisPushPackage(
        $user,
        $host,
        $package,
        $destination = null,
        $opts = [
            'password' => null,
            'key-file' => null,
            'port' => 22,
            'timeout' => 10,
        ]
    ) {
        $auth = $opts['key-file']
            ? new KeyFile($user, $opts['key-file'], $opts['password'])
            : new Password($user, $opts['password']);
        return $this->taskPushPackage($host, $auth)
            ->port($opts['port'])
            ->timeout($opts['timeout'])
            ->destinationFolder($destination)
            ->package($package)
            ->run();
    }
}
