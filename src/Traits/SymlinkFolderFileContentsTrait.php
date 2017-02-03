<?php

namespace DigipolisGent\Robo\Task\Deploy\Traits;

use DigipolisGent\Robo\Task\Deploy\SymlinkFolderFileContents;

trait SymlinkFolderFileContentsTrait {

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
}
