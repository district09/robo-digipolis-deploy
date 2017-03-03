<?php

namespace DigipolisGent\Robo\Task\Deploy\Commands;

use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\KeyFile;
use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\Password;

trait PushPackage
{
    use \DigipolisGent\Robo\Task\Deploy\Traits\PushPackageTrait;

    /**
     * Command digipolis:push-package.
     *
     * @param string $user
     *   The user command argument.
     * @param string $host
     *   The host command argument.
     * @param string $package
     *   The package command argument.
     * @param string $destination
     *   The destination command argument.
     * @param array $opts
     *   The command options.
     */
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
    ) {
        $auth = $opts['key-file']
            ? new KeyFile($user, $opts['key-file'], $opts['password'])
            : new Password($user, $opts['password']);
        return $this->taskPushPackage($host, $auth)
            ->port($opts['port'])
            ->timeout($opts['timeout'])
            ->destinationFolder($destination)
            ->package($package)
            ->run();
    }
}
