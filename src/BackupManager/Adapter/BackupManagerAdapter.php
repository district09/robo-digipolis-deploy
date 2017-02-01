<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Adapter;

use BackupManager\Manager;

class BackupManagerAdapter implements BackupManagerAdapterInterface
{
    /**
     * The backup manager.
     *
     * @var \BackupManager\Manager
     */
    protected $manager;

    public function __construct(Manager $manager)
    {
      $this->manager = $manager;
    }

    /**
     * Create the backup procedure.
     *
     * @return \DigipolisGent\Robo\Task\Deploy\BackupManager\Adapter\BackupProcedureAdapterInterface
     *   The backup procedure.
     */
    public function makeBackup()
    {
        return new BackupProcedureAdapter($this->manager->makeBackup());
    }

    /**
     * Create the restore procedure.
     *
     * @return \DigipolisGent\Robo\Task\Deploy\BackupManager\Adapter\RestoreProcedureAdapterInterface
     *   The restore procedure.
     */
    public function makeRestore()
    {
        return new RestoreProcedureAdapter($this->manager->makeRestore());
    }

}
