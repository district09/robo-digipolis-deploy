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
      'destination|d' => 'project.tar.gz',
      'destination-type|dtype' => 'local',
      'drupal' => false,
    ])
    {
        $this->createDbTask('taskDatabaseBackup', $database, $opts)
            ->destination($opts['destination'], $opts['destination-type'])
            ->run();
    }
}
