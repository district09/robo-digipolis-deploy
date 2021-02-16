<?php

namespace DigipolisGent\Tests\Robo\Task\Deploy;

use DigipolisGent\Robo\Task\Deploy\SFTP\Adapter\SFTPAdapterInterface;
use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\None;
use DigipolisGent\Tests\Robo\Task\Deploy\Mock\SFTPFactoryMock;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Robo\Common\CommandArguments;
use Robo\Contract\ConfigAwareInterface;
use Robo\Robo;
use Robo\TaskAccessor;
use Symfony\Component\Console\Output\NullOutput;

class SFTPTest extends \PHPUnit_Framework_TestCase implements ContainerAwareInterface, ConfigAwareInterface
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

    protected function mockSFTPAdapter($host, $auth, $port, $timeout)
    {
        // Mock the SFTP adapter.
        $adapter = $this->getMockBuilder(SFTPAdapterInterface::class)
            ->getMock();

        // Mock the factory.
        SFTPFactoryMock::setHost($host);
        SFTPFactoryMock::setAuth($auth);
        SFTPFactoryMock::setPort($port);
        SFTPFactoryMock::setTimeout($timeout);
        SFTPFactoryMock::setMock($adapter);
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
        $remoteFile = 'path/to/remote.txt';
        $localFile = 'path/to/local.txt';

        // Mock the SFTP adapter.
        $adapter = $this->mockSFTPAdapter($host, $auth, $port, $timeout);
        $adapter
            ->expects($this->once())
            ->method('get')
            ->with($remoteFile, $localFile)
            ->willReturn(true);
        $adapter
            ->expects($this->once())
            ->method('put')
            ->with($remoteFile, $localFile)
            ->willReturn(true);
        // Run the task.
        $result = $this
            ->taskSFTP($host, $auth)
            ->SFTPFactory(SFTPFactoryMock::class)
            ->port($port)
            ->timeout($timeout)
            ->get($remoteFile, $localFile)
            ->put($remoteFile, $localFile)
            ->run();

        $this->assertEquals(0, $result->getExitCode());
        $this->assertEquals('', $result->getMessage());
    }

    /**
     * Tests a failed 'get' run.
     */
    public function testFailedGetRun()
    {
        // Initialize variables.
        $host = 'localhost';
        $auth = new None('user');
        $port = 8022;
        $timeout = 20;
        $remoteFile = 'path/to/remote.txt';
        $localFile = 'path/to/local.txt';

        // Mock the SFTP adapter.
        $adapter = $this->mockSFTPAdapter($host, $auth, $port, $timeout);
        $adapter
            ->expects($this->once())
            ->method('get')
            ->with($remoteFile, $localFile)
            ->willReturn(false);
        $adapter
            ->expects($this->once())
            ->method('put')
            ->with($remoteFile, $localFile)
            ->willReturn(true);

        // Run the task.
        $result = $this
            ->taskSFTP($host, $auth)
            ->SFTPFactory(SFTPFactoryMock::class)
            ->port($port)
            ->timeout($timeout)
            ->get($remoteFile, $localFile)
            ->put($remoteFile, $localFile)
            ->run();

        $this->assertEquals(1, $result->getExitCode());
        $this->assertEquals(
            sprintf(
                'Could not %s file %s on %s on port %s',
                'get',
                $remoteFile,
                $host,
                $port
            ) . "\n",
            $result->getMessage()
        );
    }

    /**
     * Tests a failed 'put' run.
     */
    public function testFailedPutRun()
    {
        // Initialize variables.
        $host = 'localhost';
        $auth = new None('user');
        $port = 8022;
        $timeout = 20;
        $remoteFile = 'path/to/remote.txt';
        $localFile = 'path/to/local.txt';

        // Mock the SFTP adapter.
        $adapter = $this->mockSFTPAdapter($host, $auth, $port, $timeout);
        $adapter
            ->expects($this->once())
            ->method('get')
            ->with($remoteFile, $localFile)
            ->willReturn(true);
        $adapter
            ->expects($this->once())
            ->method('put')
            ->with($remoteFile, $localFile)
            ->willReturn(false);

        // Run the task.
        $result = $this
            ->taskSFTP($host, $auth)
            ->SFTPFactory(SFTPFactoryMock::class)
            ->port($port)
            ->timeout($timeout)
            ->get($remoteFile, $localFile)
            ->put($remoteFile, $localFile)
            ->run();

        $this->assertEquals(1, $result->getExitCode());
        $this->assertEquals(
            sprintf(
                'Could not %s file %s on %s on port %s',
                'put',
                $remoteFile,
                $host,
                $port
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
        $remoteFile = 'path/to/remote.txt';
        $localFile = 'path/to/local.txt';

        // Mock the SFTP adapter.
        $adapter = $this->mockSFTPAdapter($host, $auth, $port, $timeout);
        $adapter
            ->expects($this->once())
            ->method('get')
            ->with($remoteFile, $localFile)
            ->willReturn(false);
        $adapter
            ->expects($this->never())
            ->method('put');

        // Run the task.
        $result = $this
            ->taskSFTP($host, $auth)
            ->SFTPFactory(SFTPFactoryMock::class)
            ->stopOnFail()
            ->port($port)
            ->timeout($timeout)
            ->get($remoteFile, $localFile)
            ->put($remoteFile, $localFile)
            ->run();

        $this->assertEquals(1, $result->getExitCode());
        $this->assertEquals(
            sprintf(
                'Could not %s file %s on %s on port %s',
                'get',
                $remoteFile,
                $host,
                $port
            ),
            $result->getMessage()
        );
    }
}
