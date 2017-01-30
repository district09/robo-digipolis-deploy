<?php

namespace DigipolisGent\Robo\Task\Deploy;

use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\AbstractAuth;
use Symfony\Component\Finder\Finder;

trait loadTasks
{
    /**
     * Creates a SymlinkFolderFileContents task.
     *
     * @param string $source
     *   The directory containing the files to symlink.
     * @param string $destination
     *   The directory where the symlinks should be placed.
     * @param null|\Symfony\Component\Finder\Finder $finder
     *   The finder used to get the files from the source folder.
     *
     * @return \DigipolisGent\Robo\Task\Package\Deploy\SymlinkFolderFileContents
     *   The package project task.
     */
    protected function taskSymlinkFolderFileContents($source, $destination, Finder $finder = null)
    {
        return $this->task(SymlinkFolderFileContents::class, $source, $destination, $finder);
    }

    /**
     * @param string $host
     *   The host.
     * @param AbstractAuth $auth
     *   Authentication data.
     *
     * @return \DigipolisGent\Robo\Task\Deploy\Scp
     */
    protected function taskScp($host, AbstractAuth $auth)
    {
        return $this->task(Scp::class, $host, $auth);
    }

    /**
     * @param string $host
     *   The host.
     * @param AbstractAuth $auth
     *   Authentication data.
     *
     * @return \DigipolisGent\Robo\Task\Deploy\Ssh
     */
    protected function taskSsh($host, AbstractAuth $auth)
    {
        return $this->task(Ssh::class, $host, $auth);
    }

    /**
     * @param string $host
     *   The host.
     * @param AbstractAuth $auth
     *   Authentication data.
     *
     * @return \DigipolisGent\Robo\Task\Deploy\PushPackage
     */
    protected function taskPushPackage($host, AbstractAuth $auth)
    {
        return $this->task(PushPackage::class, $host, $auth);
    }
}
