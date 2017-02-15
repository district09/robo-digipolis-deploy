<?php

namespace DigipolisGent\Robo\Task\Deploy\Commands;

use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\KeyFile;
use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\Password;

trait PushPackage
{
    use \DigipolisGent\Robo\Task\Deploy\Traits\PushPackageTrait;

    public function digipolisPushPackage(
        $user,
        $host,
        $package,
        $destination = null,
        $opts = [
            'password' => null,
            'key-file' => null,
            'port' => 22,
            'timeout' => 10,
        ]
    )
    {
      $auth = $opts['key-file']
            ? new KeyFile($user, $opts['key-file'], $opts['password'])
            : new Password($user, $opts['password']);
        $this->taskPushPackage($host, $auth)
            ->port($opts['port'])
            ->timeout($opts['timeout'])
            ->destinationFolder($destination)
            ->package($package)
            ->run();
    }
}
