<?php

namespace DigipolisGent\Robo\Task\Deploy\Commands;

trait DatabaseBackup {

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
    ]) {
        $filesystemConfig = $opts['file-system-config']
            ?
            : $this->defaultFileSystemConfig();
        $dbConfig = $opts['database-config']
            ?
            : $this->defaultDbConfig($opts['drupal']);
        $this->taskDatabaseBackup($filesystemConfig, $dbConfig)
            ->compression($opts['comporession'])
            ->database($database)
            ->destination($opts['destination'], $opts['destination-type'])
            ->run();
    }
}
