<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Adapter;

interface BackupManagerAdapterInterface
{
    /**
     * Create the backup.
     *
     * @return \DigipolisGent\Robo\Task\Deploy\BackupManager\Adapter\BackupProcedureAdapterInterface
     */
    public function makeBackup();

    /**
     * Restore a backup.
     *
     * @return \DigipolisGent\Robo\Task\Deploy\BackupManager\Adapter\RestoreProcedureAdapterInterface
     */
    public function makeRestore();
}
