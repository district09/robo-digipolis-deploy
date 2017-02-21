<?php

namespace DigipolisGent\Robo\Task\Deploy\Traits;

use DigipolisGent\Robo\Task\Deploy\PartialCleanDirs;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

trait PartialCleanDirsTrait
{

    /**
     * Creates a new PartialCleanDir task.
     *
     * @param array $dirs
     *   The directories to clean. Either an array of directories or an array
     *   keyed by directory with the number of items to keep within this
     *   directory as value.
     * @param null|\Symfony\Component\Finder\Finder $finder
     *   The finder used to get the files from the source folder.
     * @param null|\Symfony\Component\Filesystem\Filesystem $fs
     *   Filesystem component to manipulate files.
     *
     * @return \DigipolisGent\Robo\Task\Deploy\PartialCleanDirs
     *   The partial clean directory task.
     */
    protected function taskPartialCleanDirs($dirs, Finder $finder = null, Filesystem $fs = null)
    {
        return $this->task(PartialCleanDirs::class, $dirs, $finder, $fs);
    }
}
