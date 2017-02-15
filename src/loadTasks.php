<?php

namespace DigipolisGent\Robo\Task\Deploy;

trait loadTasks
{
    use Traits\SymlinkFolderFileContentsTrait;
    use Traits\ScpTrait;
    use Traits\SshTrait;
    use Traits\PushPackageTrait;
    use Traits\DatabaseBackupTrait;
    use Traits\DatabaseRestoreTrait;
}
