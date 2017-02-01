<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Adapter;

interface BackupProcedureAdapterInterface
{

    /**
     * Run the backup procedure.
     *
     * @param string $database
     *   The database name to backup.
     * @param \BackupManager\Filesystems\Destination[] $destinations
     *   The destinations to backup to.
     * @param string $compression
     *   The compression to use for the backup.
     */
    public function run($database, array $destinations, $compression);
}
