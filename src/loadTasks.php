<?php

namespace DigipolisGent\Robo\Task\Deploy;

trait loadTasks
{
    use Traits\SymlinkFolderFileContentsTrait;
    use Traits\SFTPTrait;
    use Traits\SshTrait;
    use Traits\PushPackageTrait;
    use Traits\DatabaseBackupTrait;
    use Traits\DatabaseRestoreTrait;
    use Traits\PartialCleanDirsTrait;
    use Traits\ClearOpCacheTrait;
}
