<?php

namespace DigipolisGent\Robo\Task\Deploy\Commands;

trait DatabaseBackup
{

    use \DigipolisGent\Robo\Task\Deploy\Traits\DatabaseBackupTrait;
    use \DigipolisGent\Robo\Task\Deploy\Common\DatabaseCommand;

    /**
     * Command digipolis:database-backup.
     *
     * @param string $database
     *   The database command argument.
     * @param array $opts
     *   The command options.
     */
    public function digipolisDatabaseBackup($database = 'default', $opts = [
      'file-system-config|fsconf' => null,
      'database-config|dbconf' => null,
      'compression|c' => 'tar',
      'destination|d' => null,
      'destination-type|dtype' => 'local',
      'drupal' => false,
    ])
    {
        if (is_callable([$this, 'readProperties']))
        {
            $this->readProperties();
        }
        $destination = is_null($opts['destination'])
            ? realpath(getcwd()) . '/project.tar.gz'
            : $opts['destination'];
        return $this->createDbTask('taskDatabaseBackup', $database, $opts)
            ->destination($destination, $opts['destination-type'])
            ->run();
    }
}
