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
      'compression|c' => 'gzip',
      'source|s' => null,
      'source-type|stype' => 'local',
    ])
    {
        if (is_callable([$this, 'readProperties'])) {
            $this->readProperties();
        }
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
}
