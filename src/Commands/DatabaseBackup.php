<?php

namespace DigipolisGent\Robo\Task\Deploy\Commands;

trait DatabaseBackup {

    use \DigipolisGent\Robo\Task\Deploy\Traits\DatabaseBackupTrait;

    public function digipolisDatabaseBackup($database = 'default', $opts = [
      'file-system-config|fsconf' => null,
      'database-config|dbconf' => null,
      'compression|c' => 'tar',
      'destination|d' => 'project.tar.gz',
      'destination-type|dtype' => 'local',
      'drupal' => false,
    ])
    {
        $filesystemConfig = $opts['file-system-config']
          ?
          : ['local' => ['type' => 'Local', 'root' => realpath(getcwd())]];
        $dbConfig = $opts['database-config'];
        if (!$dbConfig) {
            $dbConfig = [
                'default' => [
                    'type' => 'mysql',
                    'host' => 'localhost',
                    'port' => '3306',
                    'user' => 'root',
                    'pass' => '',
                    'database' => dirname(realpath(getcwd())),
                ]
            ];
            if ($opts['drupal']) {
              $dbConfig = $this->parseDrupalDbConfig() ?: $dbConfig;
            }
        }
        $this->taskDatabaseBackup($filesystemConfig, $dbConfig)
          ->compression($opts['comporession'])
          ->database($database)
          ->destination($opts['destination'], $opts['destination-type'])
          ->run();
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
                // @todo structureTables
            ]
        ];
    }
}
