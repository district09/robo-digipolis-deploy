<?php

namespace DigipolisGent\Test\Robo\Task\Deploy;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Robo\Contract\ConfigAwareInterface;
use Robo\Common\CommandArguments;
use Robo\Robo;
use Robo\TaskAccessor;
use Symfony\Component\Console\Output\NullOutput;

class PartialCleanDirsTest extends \PHPUnit_Framework_TestCase implements ContainerAwareInterface, ConfigAwareInterface
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
        $dirs = array(
            'path/to/dir',
            'path/to/dir2' => 3,
            'path/to/dir3' => 10,
        );
        $finder = $this->getMockBuilder(\Symfony\Component\Finder\Finder::class)
            ->disableOriginalClone()
            ->getMock();

        $finder->expects($this->exactly(3))
            ->method('getIterator')
            ->willReturn(new \ArrayIterator(array(
                'dir1',
                'dir2',
                'dir3',
                'dir4',
                'dir5',
                'dir6',
                'dir7',
                'dir8',
                'dir9',
                'dir10',
                'dir11',
            )));


        $fs = $this->getMockBuilder(\Symfony\Component\Filesystem\Filesystem::class)
            ->getMock();

        $fs->expects($this->at(0))
            ->method('remove')
            ->with('dir6');
        $fs->expects($this->at(1))
            ->method('remove')
            ->with('dir7');
        $fs->expects($this->at(2))
            ->method('remove')
            ->with('dir8');
        $fs->expects($this->at(3))
            ->method('remove')
            ->with('dir9');
        $fs->expects($this->at(4))
            ->method('remove')
            ->with('dir10');
        $fs->expects($this->at(5))
            ->method('remove')
            ->with('dir11');

        // Delete items in path/to/dir2.
        $fs->expects($this->at(6))
            ->method('remove')
            ->with('dir4');
        $fs->expects($this->at(7))
            ->method('remove')
            ->with('dir5');
        $fs->expects($this->at(8))
            ->method('remove')
            ->with('dir6');
        $fs->expects($this->at(9))
            ->method('remove')
            ->with('dir7');
        $fs->expects($this->at(10))
            ->method('remove')
            ->with('dir8');
        $fs->expects($this->at(11))
            ->method('remove')
            ->with('dir9');
        $fs->expects($this->at(12))
            ->method('remove')
            ->with('dir10');
        $fs->expects($this->at(13))
            ->method('remove')
            ->with('dir11');

        // Delete items in path/to/dir3.
        $fs->expects($this->at(14))
            ->method('remove')
            ->with('dir11');

        $result = $this->taskPartialCleanDirs($dirs, $finder, $fs)
          ->run();

        $this->assertEquals('', $result->getMessage());
        $this->assertEquals(0, $result->getExitCode());
    }
}
