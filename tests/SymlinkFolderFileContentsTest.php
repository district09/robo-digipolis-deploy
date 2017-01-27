<?php

namespace DigipolisGent\Robo\Task\Deploy\Test;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Robo\Contract\ConfigAwareInterface;
use Robo\Common\CommandArguments;
use Robo\Robo;
use Robo\TaskAccessor;
use Symfony\Component\Console\Output\NullOutput;

class SymlinkFolderFileContentsTest extends \PHPUnit_Framework_TestCase implements ContainerAwareInterface, ConfigAwareInterface
{

    use \DigipolisGent\Robo\Task\Deploy\loadTasks;
    use TaskAccessor;
    use ContainerAwareTrait;
    use CommandArguments;
    use \Robo\Task\Base\loadTasks;
    use \Robo\Common\ConfigAwareTrait;

    /**
     * Set up the Robo container so that we can create tasks in our tests.
     */
    public function setUp()
    {
        $container = Robo::createDefaultContainer(null, new NullOutput());
        $this->setContainer($container);
        $this->setConfig(Robo::config());
    }

    /**
     * Scaffold the collection builder.
     *
     * @return \Robo\Collection\CollectionBuilder
     *   The collection builder.
     */
    public function collectionBuilder()
    {
        $emptyRobofile = new \Robo\Tasks();

        return $this->getContainer()
            ->get('collectionBuilder', [$emptyRobofile]);
    }

    public function testRun() {
        $source = __DIR__ . '/../testfiles/source';
        $destination = __DIR__ . '/../testfiles/destination';
        $result = $this->taskSymlinkFolderFileContents($source, $destination)->run();
        $this->assertEquals('', $result->getMessage());
        $this->assertEquals(0, $result->getExitCode());
        $dirs = [
            'folder',
            'folder/subfolder',
        ];
        foreach ($dirs as $dir) {
          $this->assertTrue(is_dir(realpath($destination . '/' . $dir)));
        }
        $symlinks = [
            'folder/file',
            'folder/subfolder/.gitkeep',
            'folder/subfolder/testfile',
        ];
        foreach ($symlinks as $symlink) {
          $this->assertTrue(
              is_link(realpath($destination) . '/' . $symlink),
              'Failed asserting that ' . realpath($destination) . '/' . $symlink . ' is a symlink.'
          );
          $this->assertEquals(
              realpath($source . '/' . $symlink),
              realpath($destination . '/' . $symlink),
              'Failed asserting that the symlink '
                  . realpath($destination) . '/' . $symlink
                  . ' points to ' . realpath($source . '/' . $symlink)
          );
        }
        // Cleanup.
        exec('rm -rf ' . realpath($destination) . '/folder');
    }
}
