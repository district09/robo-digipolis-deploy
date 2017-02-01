<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Adapter;

use BackupManager\Procedures\BackupProcedure;

class BackupProcedureAdapter implements BackupProcedureAdapterInterface
{
    protected $procedure;

    public function __construct(BackupProcedure $procedure)
    {
        $this->procedure = $procedure;
    }
    /**
     * {@inheritdoc}
     */
    public function run($database, array $destinations, $compression)
    {
        return $this->procedure->run($database, $destinations, $compression);
    }

}
