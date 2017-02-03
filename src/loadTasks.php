<?php

namespace DigipolisGent\Robo\Task\Deploy;

use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\AbstractAuth;

trait loadTasks
{
    use Traits\SymlinkFolderFileContentsTrait;
    use Traits\ScpTrait;
    use Traits\SshTrait;
    use Traits\PushPackageTrait;
    use Traits\DatabaseBackupTrait;
    use Traits\DatabaseRestoreTrait;
}
