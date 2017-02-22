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
      'source|s' => null,
      'source-type|stype' => 'local',
      'drupal' => false,
    ])
    {
        $source = is_null($opts['source'])
            ? realpath(getcwd()) . '/project.tar.gz'
            : $opts['source'];
        if (is_null($opts['file-system-config'])) {
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
