<?php

namespace DigipolisGent\Robo\Task\Deploy\Commands;

use DigipolisGent\Robo\Task\Deploy\PartialCleanDirs as PartialCleanDirsTask;

trait PartialCleanDirs
{

    use \DigipolisGent\Robo\Task\Deploy\Traits\PartialCleanDirsTrait;

    /**
     * Partially clean directories.
     *
     * @param array $dirs
     *   Comma separated list of directories to clean. Each dir is optionally
     *   followed by a colon and the number of items to preserve in that dir.
     *   Defaults to 5 items.
     * @param array $opts
     *   The command options.
     */
    public function digipolisCleanDir($dirs, $opts = ['sort' => PartialCleanDirsTask::SORT_NAME])
    {
        $dirsArg = array();
        foreach (array_map('trim', explode(',', $dirs)) as $dir) {
            $dirParts = explode(':', $dir);
            if (count($dirParts) > 1) {
                $dirsArg[$dirParts[0]] = $dirParts[1];
                continue;
            }
            $dirsArg[] = $dirParts[0];
        }
        return $this->taskPartialCleanDirs($dirsArg)
            ->sortBy($opts['sort'])
            ->run();
    }
}
