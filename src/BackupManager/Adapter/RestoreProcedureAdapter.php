<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Adapter;

use BackupManager\Procedures\RestoreProcedure;

class RestoreProcedureAdapter implements RestoreProcedureAdapterInterface
{
    protected $procedure;

    public function __construct(RestoreProcedure $procedure)
    {
        $this->procedure = $procedure;
    }

    /**
     * {@inheritdoc}
     */
    public function run($sourceType, $sourcePath, $databaseName, $compression = null)
    {
        return $this->procedure->run($sourceType, $sourcePath, $databaseName, $compression);
    }
}
