<?php

namespace DigipolisGent\Robo\Task\Deploy;

use Robo\Task\BaseTask;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class PartialCleanDirs extends BaseTask
{
    /**
     * Sort by name.
     *
     * @const PartialCleanDir::SORT_NAME Sort by name.
     */
    const SORT_NAME = 'name';

    /**
     * This is the time that the file was last accessed, read or written to.
     *
     * @const PartialCleanDir::SORT_ACCESS_TIME Sort by access time.
     */
    const SORT_ACCESS_TIME = 'access_time';

    /**
     * This is the last time the actual contents of the file were last modified.
     *
     * @const PartialCleanDir::SORT_ACCESS_TIME Sort by modified time.
     */
    const SORT_MODIFIED_TIME = 'modified_time';

    /**
     * This is the time that the inode information was last modified
     * (permissions, owner, group or other metadata).
     *
     * @const PartialCleanDir::SORT_CHANGED_TIME Sort by changed time.
     */
    const SORT_CHANGED_TIME = 'changed_time';

    /**
     * Sorts files and directories by type (directories before files),then by
     * name.
     *
     * @const PartialCleanDir::SORT_TYPE Sort by type.
     */
    const SORT_TYPE = 'type';

    /**
     * The directories to clean.
     *
     * @var string
     */
    protected $dirs = [];

    /**
     * The finder used to get the files from the source directories.
     *
     * @var \Symfony\Component\Finder\Finder
     */
    protected $finder;

    /**
     * Filesystem component.
     *
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fs;

    /**
     * What the sort the items in the directories by.
     *
     * @var string|\Closure
     */
    protected $sort;

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
     */
    public function __construct(array $dirs, Finder $finder = null, Filesystem $fs = null)
    {
        $this->dirs($dirs);
        $this->finder = is_null($finder)
            ? (new Finder())->ignoreDotFiles(false)->ignoreVCS(false)
            : $finder;
        $this->fs = is_null($fs)
            ? new Filesystem()
            : $fs;
        $this->sort = static::SORT_NAME;
    }

    /**
     * Set the finder to use.
     *
     * @param Finder $finder
     *
     * @return $this
     */
    public function finder(Finder $finder)
    {
        $this->finder = $finder;

        return $this;
    }

    /**
     * Set the filesystem component to use.
     *
     * @param Filesystem $fs
     *
     * @return $this
     */
    public function fileSystem(Filesystem $fs)
    {
        $this->fs = $fs;

        return $this;
    }

    /**
     * Add directories to clean.
     *
     * @param array $dirs
     *   Either an array of directories or an array keyed by directory with the
     *   number of items to keep within this directory as value.
     *
     * @return $this
     */
    public function dirs(array $dirs)
    {
        foreach ($dirs as $k => $v) {
            if (is_numeric($v)) {
                $this->dir($k, $v);
                continue;
            }
            $this->dir($v);
        }

        return $this;
    }

    /**
     * Add a directory to clean.
     *
     * @param $dir
     *   The directory to clean.
     * @param $keep
     *   The number of items to keep in this directory. Defaults to 5.
     *
     * @return $this
     */
    public function dir($dir, $keep = 5)
    {
        $this->dirs[$dir] = $keep;

        return $this;
    }

    /**
     * What to sort the folder items by. The last x items (as set by the dir()
     * method) returned after the sort method will be kept. When using one of
     * the PartialCleanDir::SORT_* constants, items will be sorted ascending. To
     * overwrite this behavior you should provide your own sort function as the
     * $sort parameter.
     *
     * @param string|\Closure $sort
     *   One of the PartialCleanDir::SORT_* constants or an anonymous function. The
     *   anonymous function receives two \SplFileInfo instances to compare.
     *
     * @return $this
     */
    public function sortBy($sort = self::SORT_NAME)
    {
        $this->sort = $sort;

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function run()
    {
        try {
            foreach ($this->dirs as $dir => $keep) {
                if ($dir) {
                    $this->cleanDir($dir, $keep);
                }
            }
        } catch (\Exception $e) {
            return \Robo\Result::fromException($this, $e);
        }
        return \Robo\Result::success($this);
    }

    /**
     * Clean a directory.
     *
     * @param string $dir
     *   The directory to clean.
     * @param int $keep
     *   The number of items to keep.
     */
    protected function cleanDir($dir, $keep)
    {
        $finder = clone $this->finder;
        $finder->in($dir);
        $finder->depth(0);
        switch ($this->sort) {
            case static::SORT_NAME:
                $finder->sortByName();
                break;

            case static::SORT_TYPE:
                $finder->sortByType();
                break;

            case static::SORT_ACCESS_TIME:
                $finder->sortByAccessedTime();
                break;

            case static::SORT_MODIFIED_TIME:
                $finder->sortByModifiedTime();
                break;

            case static::SORT_CHANGED_TIME:
                $finder->sortByType();
                break;

            case $this->sort instanceof \Closure:
                $finder->sort($this->sort);
                break;
        }
        $items = iterator_to_array($finder->getIterator());
        if ($keep) {
            array_splice($items, -$keep);
        }
        while ($items) {
            $item = reset($items);
            try {
                // To delete a file we must have access rights on the parent
                // directory.
                $this->fs->chmod(dirname(realpath($item)), 0777, 0000, true);
                $this->fs->chmod($item, 0777, 0000, true);
            } catch (IOException $e) {
                // If chmod didn't work and the exception contains a path, try
                // to remove anyway.
                $path = $e->getPath();
                if ($path && realpath($path) !== realpath($item)) {
                    $this->fs->remove($path);
                    continue;
                }

            }
            $this->fs->remove($item);
            array_shift($items);
        }
    }
}
