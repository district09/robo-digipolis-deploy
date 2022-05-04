<?php

namespace DigipolisGent\Robo\Task\Deploy;

trait Tasks
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
