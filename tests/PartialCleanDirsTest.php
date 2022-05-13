<?php

namespace DigipolisGent\Test\Robo\Task\Deploy;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use PHPUnit\Framework\TestCase;
use Robo\Collection\CollectionBuilder;
use Robo\Common\CommandArguments;
use Robo\Contract\ConfigAwareInterface;
use Robo\Robo;
use Robo\TaskAccessor;
use Symfony\Component\Console\Output\NullOutput;

class PartialCleanDirsTest extends TestCase implements ContainerAwareInterface, ConfigAwareInterface
{

    use \DigipolisGent\Robo\Task\Deploy\Tasks;
    use TaskAccessor;
    use ContainerAwareTrait;
    use CommandArguments;
    use \Robo\Task\Base\Tasks;
    use \Robo\Common\ConfigAwareTrait;

    /**
     * Set up the Robo container so that we can create tasks in our tests.
     */
    public function setUp(): void
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

        return CollectionBuilder::create($this->getContainer(), $emptyRobofile);
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

        $fs->expects($this->exactly(15))
            ->method('remove')
            ->withConsecutive(
                ['dir1'],
                ['dir2'],
                ['dir3'],
                ['dir4'],
                ['dir5'],
                ['dir6'],
                // Delete items in path/to/dir2.
                ['dir1'],
                ['dir2'],
                ['dir3'],
                ['dir4'],
                ['dir5'],
                ['dir6'],
                ['dir7'],
                ['dir8'],
                // Delete items in path/to/dir3.
                ['dir1'],
            );

        $fs->expects($this->exactly(15))
            ->method('chmod')
            ->withConsecutive(
                ['dir1', 0777, 0000, true],
                ['dir2', 0777, 0000, true],
                ['dir3', 0777, 0000, true],
                ['dir4', 0777, 0000, true],
                ['dir5', 0777, 0000, true],
                ['dir6', 0777, 0000, true],
                // Delete items in path/to/dir2.
                ['dir1', 0777, 0000, true],
                ['dir2', 0777, 0000, true],
                ['dir3', 0777, 0000, true],
                ['dir4', 0777, 0000, true],
                ['dir5', 0777, 0000, true],
                ['dir6', 0777, 0000, true],
                ['dir7', 0777, 0000, true],
                ['dir8', 0777, 0000, true],
                // Delete items in path/to/dir3.
                ['dir1', 0777, 0000, true],
            );

        $result = $this->taskPartialCleanDirs($dirs, $finder, $fs)
          ->run();

        $this->assertEquals('', $result->getMessage());
        $this->assertEquals(0, $result->getExitCode());
    }
}
