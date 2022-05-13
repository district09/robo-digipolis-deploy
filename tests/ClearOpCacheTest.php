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

class ClearOpCacheTest extends TestCase implements ContainerAwareInterface, ConfigAwareInterface
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
        $task = $this->taskClearOpCache(\DigipolisGent\Robo\Task\Deploy\ClearOpCache::ENV_FCGI, '127.0.0.1:9000');
        $cmd = realpath(__DIR__ . '/..') . '/vendor/bin/cachetool opcache:reset --fcgi=127.0.0.1:9000';
        $this->assertEquals($cmd, $task->getCommand());
    }
}
