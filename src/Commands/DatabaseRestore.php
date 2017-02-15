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
        $this->createDbTask('taskDatabaseRestore', $database, $opts)
            ->source($opts['source'], $opts['source-type'])
            ->run();
    }
}
