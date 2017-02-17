<?php

namespace DigipolisGent\Tests\Robo\Task\Deploy;

use DigipolisGent\Robo\Task\Deploy\Scp\Adapter\ScpAdapterInterface;
use DigipolisGent\Robo\Task\Deploy\Ssh\Adapter\SshAdapterInterface;
use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\None;
use DigipolisGent\Tests\Robo\Task\Deploy\Mock\ScpFactoryMock;
use DigipolisGent\Tests\Robo\Task\Deploy\Mock\SshFactoryMock;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Robo\Common\CommandArguments;
use Robo\Contract\ConfigAwareInterface;
use Robo\Robo;
use Robo\TaskAccessor;
use Symfony\Component\Console\Output\NullOutput;

class PushPackageTest extends \PHPUnit_Framework_TestCase implements ContainerAwareInterface, ConfigAwareInterface
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
        $adapter->expects($this->any())
            ->method('getExitStatus')
            ->willReturn(0);

        // Mock the factory.
        SshFactoryMock::setHost($host);
        SshFactoryMock::setPort($port);
        SshFactoryMock::setTimeout($timeout);
        SshFactoryMock::setMock($adapter);
        return $adapter;
    }

    protected function mockScpAdapter($host, $auth, $port, $timeout)
    {
        // Mock the scp adapter.
        $adapter = $this->getMockBuilder(ScpAdapterInterface::class)
            ->getMock();

        // Mock the factory.
        ScpFactoryMock::setHost($host);
        ScpFactoryMock::setAuth($auth);
        ScpFactoryMock::setPort($port);
        ScpFactoryMock::setTimeout($timeout);
        ScpFactoryMock::setMock($adapter);
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
    public function testRun()
    {
        // Initialize variables.
        $host = 'localhost';
        $auth = new None('user');
        $port = 8022;
        $timeout = 20;
        $untar = 'tar -xzf local.tar.gz';
        $remove = 'rm -rf local.tar.gz';
        $destinationFolder = 'path/to/remote';
        $localFile = 'path/to/local.tar.gz';

        // Mock the ssh adapter.
        $sshAdapter = $this->mockSshAdapter($host, $port, $timeout);
        $sshAdapter
            ->expects($this->at(0))
            ->method('login');
        $sshAdapter
            ->expects($this->at(1))
            ->method('exec')
            ->with('mkdir -p ' . $destinationFolder, null)
            ->willReturn(true);

        $sshAdapter
            ->expects($this->at(3))
            ->method('exec')
            ->with('cd ' . $destinationFolder, null)
            ->willReturn(true);

        $sshAdapter
            ->expects($this->at(4))
            ->method('exec')
            ->with($untar, null)
            ->willReturn(true);

        $sshAdapter
            ->expects($this->at(5))
            ->method('exec')
            ->with($remove, null)
            ->willReturn(true);

        // Mock the SCP adapter.
        $adapter = $this->mockScpAdapter($host, $auth, $port, $timeout);
        $adapter
            ->expects($this->once())
            ->method('put')
            ->with($destinationFolder . DIRECTORY_SEPARATOR . basename($localFile), $localFile)
            ->willReturn(true);

        // Run the task.
        $result = $this
            ->taskPushPackage($host, $auth)
            ->sshFactory(SshFactoryMock::class)
            ->scpFactory(ScpFactoryMock::class)
            ->port($port)
            ->timeout($timeout)
            ->destinationFolder($destinationFolder)
            ->package($localFile)
            ->run();


        $this->assertEquals('', $result->getMessage());
        $this->assertEquals(0, $result->getExitCode());
    }
}
