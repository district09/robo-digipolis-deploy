<?php

namespace DigipolisGent\Robo\Task\Deploy;

use Robo\Collection\CollectionBuilder;
use Robo\Contract\BuilderAwareInterface;
use Robo\Task\BaseTask;
use Robo\Task\Filesystem\FilesystemStack;
use Symfony\Component\Finder\Finder;

class SymlinkFolderFileContents extends BaseTask implements BuilderAwareInterface
{
    use \Robo\TaskAccessor;

    /**
     * The directory containing the files to symlink.
     *
     * @var string
     */
    protected $source;

    /**
     * The directory where the symlinks should be placed.
     *
     * @var string
     */
    protected $destination;

    /**
     * The finder used to get the files from the source folder.
     *
     * @var \Symfony\Component\Finder\Finder
     */
    protected $finder;

    /**
     * Creates a new SymlinkFolderFileContents task.
     *
     * @param string $source
     *   The directory containing the files to symlink.
     * @param string $destination
     *   The directory where the symlinks should be placed.
     * @param null|\Symfony\Component\Finder\Finder $finder
     *   The finder used to get the files from the source folder.
     */
    public function __construct($source, $destination, $finder = null)
    {
        $this->source = realpath($source);
        $this->destination = realpath($destination);
        $this->finder = is_null($finder)
            ? (new Finder())->files()->ignoreDotFiles(false)->ignoreVCS(false)
            : $finder;
    }

    public function finder(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->finder->in($this->source);
        $task = $this->task(FilesystemStack::class);
        foreach ($this->finder as $item) {
            $relativePath = $item->getRelativePathname();
            $pathParts = array_filter(explode('/', $relativePath));
            // We don't need to create the last item in the path, as that will
            // be the symlink.
            array_pop($pathParts);
            $this->ensureDestinationPath($pathParts, $task);
            $task->symlink($this->source . '/' . $relativePath, $this->destination . '/' . $relativePath);
        }
        return $task->run();
    }

    /**
     * Ensures a path in the destination folder exists. Creates it if not.
     *
     * @param array $pathParts
     *   The path to ensure, exploded to an array.
     * @param \Robo\Collection\CollectionBuilder $stack
     *   The file system stack wrapped by a collection builder that will create
     *   the directories if they don't exist.
     *
     * @return $this
     */
    protected function ensureDestinationPath(array $pathParts, CollectionBuilder $stack)
    {
        $path = $this->destination;
        while ($folder = array_shift($pathParts)) {
            $path .= '/' . $folder;
            if (!file_exists($path)) {
                $stack->mkdir($path);
            }
        }
        return $this;
    }
}
