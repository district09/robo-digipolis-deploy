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
      'compression|c' => 'gzip',
      'destination|d' => null,
      'destination-type|dtype' => 'local',
    ])
    {
        if (is_callable([$this, 'readProperties'])) {
            $this->readProperties();
        }
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
}
