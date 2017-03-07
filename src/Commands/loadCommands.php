<?php

namespace DigipolisGent\Robo\Task\Deploy\Commands;

trait loadCommands
{
    use DatabaseBackup, DatabaseRestore {
        DatabaseBackup::defaultFileSystemConfig insteadof DatabaseRestore;
        DatabaseBackup::defaultDbConfig insteadof DatabaseRestore;
        DatabaseBackup::createDbTask insteadof DatabaseRestore;
    }
    use PushPackage;
    use ClearOpCache;
    use PartialCleanDirs;
}
