<?php

namespace DigipolisGent\Tests\Robo\Task\Deploy;

use DigipolisGent\Robo\Task\Deploy\Ssh\Adapter\SshAdapterInterface;
use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\None;
use DigipolisGent\Tests\Robo\Task\Deploy\Mock\SshFactoryMock;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Robo\Common\CommandArguments;
use Robo\Contract\ConfigAwareInterface;
use Robo\Robo;
use Robo\TaskAccessor;
use Symfony\Component\Console\Output\NullOutput;

class SshTest extends \PHPUnit_Framework_TestCase implements ContainerAwareInterface, ConfigAwareInterface
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

    protected function mockSshAdapter($host, $port, $timeout)
    {
        // Mock the scp adapter.
        $adapter = $this->getMockBuilder(SshAdapterInterface::class)
            ->getMock();

        // Mock the factory.
        SshFactoryMock::setHost($host);
        SshFactoryMock::setPort($port);
        SshFactoryMock::setTimeout($timeout);
        SshFactoryMock::setMock($adapter);
        return $adapter;
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

    /**
     * Tests a successful run.
     */
    public function testSuccessfulRun()
    {
        // Initialize variables.
        $host = 'localhost';
        $auth = new None('user');
        $port = 8022;
        $timeout = 20;
        $command = 'which composer';
        $dir = 'path/to/dir';
        $map = array(
            array('cd ' . $dir, null, ''),
            array($command, null, ''),
        );

        // Mock the ssh adapter.
        $adapter = $this->mockSshAdapter($host, $port, $timeout);
        $adapter
            ->expects($this->at(0))
            ->method('login');
        $adapter
            ->expects($this->exactly(2))
            ->method('exec')
            ->will($this->returnValueMap($map));
        $adapter->expects($this->exactly(2))
            ->method('getExitStatus')
            ->will($this->onConsecutiveCalls(0, 0));

        // Run the task.
        $result = $this
            ->taskSsh($host, $auth)
            ->sshFactory(SshFactoryMock::class)
            ->port($port)
            ->timeout($timeout)
            ->remoteDirectory($dir)
            ->exec($command)
            ->run();


        $this->assertEquals('', $result->getMessage());
        $this->assertEquals(0, $result->getExitCode());
    }

    /**
     * Tests a failed 'get' run.
     */
    public function testFailedRun()
    {
        // Initialize variables.
        $host = 'localhost';
        $auth = new None('user');
        $port = 8022;
        $timeout = 20;
        $command = 'which composer';
        $dir = 'path/to/dir';
        $map = array(
            array('cd ' . $dir, null, ''),
            array($command, null, 1),
        );

        // Mock the ssh adapter.
        $adapter = $this->mockSshAdapter($host, $port, $timeout);
        $adapter
            ->expects($this->at(0))
            ->method('login');
        $adapter
            ->expects($this->exactly(2))
            ->method('exec')
            ->will($this->returnValueMap($map));
         $adapter->expects($this->exactly(2))
            ->method('getExitStatus')
            ->will($this->onConsecutiveCalls(0, 1));

        $adapter
            ->expects($this->once())
            ->method('getStdError')
            ->willReturn('Something went wrong');


        // Run the task.
        $result = $this
            ->taskSsh($host, $auth)
            ->sshFactory(SshFactoryMock::class)
            ->port($port)
            ->timeout($timeout)
            ->remoteDirectory($dir)
            ->exec($command)
            ->run();

        $this->assertEquals(1, $result->getExitCode());
        $this->assertEquals(
            sprintf(
                'Could not execute %s on %s on port %s with message: %s',
                $command,
                $host,
                $port,
                'Something went wrong'
            ) . "\n",
            $result->getMessage()
        );
    }

    /**
     * Test stopOnFail.
     */
    public function testStopOnFail()
    {
        // Initialize variables.
        $host = 'localhost';
        $auth = new None('user');
        $port = 8022;
        $timeout = 20;
        $command = 'which composer';
        $dir = 'path/to/dir';
        $map = array(
            array('cd ' . $dir, null, ''),
            array($command, null, 1),
        );

        // Mock the ssh adapter.
        $adapter = $this->mockSshAdapter($host, $port, $timeout);
        $adapter
            ->expects($this->at(0))
            ->method('login');
        $adapter
            ->expects($this->exactly(2))
            ->method('exec')
            ->will($this->returnValueMap($map));
         $adapter->expects($this->exactly(2))
            ->method('getExitStatus')
            ->will($this->onConsecutiveCalls(0, 1));

        $adapter
            ->expects($this->once())
            ->method('getStdError')
            ->willReturn('Something went wrong');


        // Run the task.
        $result = $this
            ->taskSsh($host, $auth)
            ->sshFactory(SshFactoryMock::class)
            ->stopOnFail()
            ->port($port)
            ->timeout($timeout)
            ->remoteDirectory($dir)
            ->exec($command)
            ->run();

        $this->assertEquals(1, $result->getExitCode());
        $this->assertEquals(
            sprintf(
                'Could not execute %s on %s on port %s with message: %s',
                $command,
                $host,
                $port,
                'Something went wrong'
            ),
            $result->getMessage()
        );
    }
}
