<?php

namespace DigipolisGent\Tests\Robo\Task\Deploy;

use DigipolisGent\Robo\Task\Deploy\Ssh\Adapter\SshAdapterInterface;
use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\None;
use DigipolisGent\Tests\Robo\Task\Deploy\Mock\SshFactoryMock;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use PHPUnit\Framework\TestCase;
use Robo\Collection\CollectionBuilder;
use Robo\Common\CommandArguments;
use Robo\Contract\ConfigAwareInterface;
use Robo\Robo;
use Robo\TaskAccessor;
use Symfony\Component\Console\Output\NullOutput;

class SshTest extends TestCase implements ContainerAwareInterface, ConfigAwareInterface
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

    protected function mockSshAdapter($host, $port, $timeout)
    {
        // Mock the ssh adapter.
        $adapter = $this->getMockBuilder(SshAdapterInterface::class)
            ->disableOriginalConstructor()
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

        return CollectionBuilder::create($this->getContainer(), $emptyRobofile);
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

        // Mock the ssh adapter.
        $adapter = $this->mockSshAdapter($host, $port, $timeout);
        $adapter
            ->expects($this->once())
            ->method('login');
        $adapter
            ->expects($this->exactly(1))
            ->method('exec')
            ->with('cd ' . $dir . ' && ' . $command, $this->callback('is_callable'))
            ->willReturn('');
        $adapter->expects($this->exactly(1))
            ->method('getExitStatus')
            ->willReturn(0);

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

    public function testPhysicalRemoteDir()
    {
        // Initialize variables.
        $host = 'localhost';
        $auth = new None('user');
        $port = 8022;
        $timeout = 20;
        $command = 'which composer';
        $dir = 'path/to/dir';

        // Mock the ssh adapter.
        $adapter = $this->mockSshAdapter($host, $port, $timeout);
        $adapter
            ->expects($this->once())
            ->method('login');
        $adapter
            ->expects($this->exactly(1))
            ->method('exec')
             ->with('cd -P ' . $dir . ' && ' . $command, $this->callback('is_callable'))
            ->willReturn('');
        $adapter->expects($this->exactly(1))
            ->method('getExitStatus')
            ->willReturn(0);

        // Run the task.
        $result = $this
            ->taskSsh($host, $auth)
            ->sshFactory(SshFactoryMock::class)
            ->port($port)
            ->timeout($timeout)
            ->remoteDirectory($dir, true)
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

        // Mock the ssh adapter.
        $adapter = $this->mockSshAdapter($host, $port, $timeout);
        $adapter
            ->expects($this->once())
            ->method('login');
        $adapter
            ->expects($this->exactly(1))
            ->method('exec')
             ->with('cd ' . $dir . ' && ' . $command, $this->callback('is_callable'))
            ->willReturn('');
        $adapter->expects($this->exactly(1))
            ->method('getExitStatus')
            ->willReturn(1);

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
                'Could not execute %s on %s on port %s in folder %s with message: %s.',
                $command,
                $host,
                $port,
                $dir,
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

        // Mock the ssh adapter.
        $adapter = $this->mockSshAdapter($host, $port, $timeout);
        $adapter
            ->expects($this->once())
            ->method('login');
        $adapter
            ->expects($this->exactly(1))
            ->method('exec')
             ->with('cd ' . $dir . ' && ' . $command, $this->callback('is_callable'))
            ->willReturn('');
        $adapter->expects($this->exactly(1))
            ->method('getExitStatus')
            ->willReturn(1);

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
                'Could not execute %s on %s on port %s in folder %s with message: %s.',
                $command,
                $host,
                $port,
                $dir,
                'Something went wrong'
            ),
            $result->getMessage()
        );
    }
}
