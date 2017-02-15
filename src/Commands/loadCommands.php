<?php

namespace DigipolisGent\Robo\Task\Deploy\Commands;

trait loadCommands
{
    use DatabaseBackup;
    use DatabaseRestore;
    use PushPackage;
}
