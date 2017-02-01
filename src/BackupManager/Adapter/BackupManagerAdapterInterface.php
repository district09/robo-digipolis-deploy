<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Adapter;

interface BackupManagerAdapterInterface {
    /**
     * Create the backup.
     *
     * @return ProcedureAdapter
     */
    public function makeBackup();

    /**
     * @return ProcedureAdapter
     */
    public function makeRestore();

}
