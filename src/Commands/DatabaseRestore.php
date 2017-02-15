<?php

namespace DigipolisGent\Robo\Task\Deploy\Commands;

trait DatabaseRestore
{

    use \DigipolisGent\Robo\Task\Deploy\Traits\DatabaseRestoreTrait;
    use \DigipolisGent\Robo\Task\Deploy\Common\DatabaseCommand;

    /**
     * Command digipolis:database-restore.
     *
     * @param string $database
     *   The database command argument.
     * @param array $opts
     *   The command options.
     */
    public function digipolisDatabaseRestore($database = 'default', $opts = [
      'file-system-config|fsconf' => null,
      'database-config|dbconf' => null,
      'compression|c' => 'tar',
      'source|s' => 'project.tar.gz',
      'source-type|stype' => 'local',
      'drupal' => false,
    ])
    {
        $filesystemConfig = $opts['file-system-config']
            ?
            : $this->defaultFileSystemConfig();
        $dbConfig = $opts['database-config']
            ?
            : $this->defaultDbConfig($opts['drupal']);
        $this->taskDatabaseRestore($filesystemConfig, $dbConfig)
            ->compression($opts['comporession'])
            ->database($database)
            ->source($opts['source'], $opts['source-type'])
            ->run();
    }
}
