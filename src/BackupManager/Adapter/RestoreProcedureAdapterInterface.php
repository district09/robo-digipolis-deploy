<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Adapter;

interface RestoreProcedureAdapterInterface {

    /**
     * Run the restore procedure.
     *
     * @param string $sourceType
     *   Type of the filesystem where the source is located.
     * @param string $sourcePath
     *   Path to the source file.
     * @param string $databaseName
     *   Name of the database.
     * @param string $compression
     *   Type of compression on the source file (optional).
     */
    public function run($sourceType, $sourcePath, $databaseName, $compression = null);
}
