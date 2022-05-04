<?php

namespace DigipolisGent\Tests\Robo\Task\Deploy;

use DigipolisGent\Robo\Task\Deploy\BackupManager\Adapter\BackupManagerAdapterInterface;
use DigipolisGent\Robo\Task\Deploy\BackupManager\Adapter\BackupProcedureAdapterInterface;
use DigipolisGent\Robo\Task\Deploy\BackupManager\Adapter\RestoreProcedureAdapterInterface;
use DigipolisGent\Robo\Task\Deploy\Tasks;
use DigipolisGent\Tests\Robo\Task\Deploy\Mock\BackupManagerFactoryMock;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use PHPUnit\Framework\TestCase;
use Robo\Collection\CollectionBuilder;
use Robo\Common\CommandArguments;
use Robo\Contract\ConfigAwareInterface;
use Robo\Robo;
use Robo\TaskAccessor;
use Symfony\Component\Console\Output\NullOutput;

class DatabaseRestoreTest extends TestCase implements ContainerAwareInterface, ConfigAwareInterface
{

    use Tasks;
    use TaskAccessor;
    use ContainerAwareTrait;
    use CommandArguments;
    use \Robo\Task\Base\Tasks;
    use \Robo\Common\ConfigAwareTrait;

    protected $filesystemConfig;
    protected $dbConfig;

    /**
     * Set up the Robo container so that we can create tasks in our tests.
     */
    public function setUp(): void
    {
        $container = Robo::createDefaultContainer(null, new NullOutput());
        $this->setContainer($container);
        $this->setConfig(Robo::config());
        $this->dbConfig = [
            'development' => [
                'type' => 'mysql',
                'host' => 'localhost',
                'port' => '3306',
                'user' => 'root',
                'pass' => 'password',
                'database' => 'test',
                'singleTransaction' => false,
                'ignoreTables' => [],
            ],
            'production' => [
                'type' => 'mysql',
                'host' => 'localhost',
                'port' => '3306',
                'user' => 'root',
                'pass' => 'password',
                'database' => 'test',
                'ignoreTables' => [],
                'structureTables' => ['cache'],
                'tables' => [],
                'dataOnly' => false,
                'orderedDump' => false,
                'singleTransaction' => true,
                'extra' => '--opt',
            ],
        ];

        $this->filesystemConfig = [
            'local' => [
                'type' => 'Local',
                'root' => realpath(__DIR__ . '/../testfiles'),
            ],
        ];
    }

    protected function mockManagerAdapter()
    {

        // Mock the backup manager adapter.
        $adapter = $this->getMockBuilder(BackupManagerAdapterInterface::class)
            ->getMock();

        // Mock the factory.
        BackupManagerFactoryMock::setFilesystemConfig($this->filesystemConfig);
        BackupManagerFactoryMock::setDbConfig($this->dbConfig);
        BackupManagerFactoryMock::setMock($adapter);
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
    public function testRun()
    {
        $manager = $this->mockManagerAdapter();
        $procedure = $this->getMockBuilder(RestoreProcedureAdapterInterface::class)
            ->getMock();

        $procedure
            ->expects($this->once())
            ->method('run')
            ->willReturn(null);
        $manager
            ->expects($this->once())
            ->method('makeRestore')
            ->willReturn($procedure);

        $result = $this->taskDatabaseRestore($this->filesystemConfig, $this->dbConfig)
            ->database('development')
            ->source('')
            ->compression('tar')
            ->backupManagerFactory(BackupManagerFactoryMock::class)
            ->run();

        $this->assertEquals('', $result->getMessage());
        $this->assertEquals(0, $result->getExitCode());
    }

    /**
     * Tests a failed run.
     */
    public function testFailedRun()
    {
        $manager = $this->mockManagerAdapter();
        $procedure = $this->getMockBuilder(RestoreProcedureAdapterInterface::class)
            ->getMock();

        $procedure
            ->expects($this->once())
            ->method('run')
            ->willThrowException(new \Exception('Something went wrong!'));
        $manager
            ->expects($this->once())
            ->method('makeRestore')
            ->willReturn($procedure);

        $result = $this->taskDatabaseRestore($this->filesystemConfig, $this->dbConfig)
            ->database('development')
            ->source('')
            ->compression('tar')
            ->backupManagerFactory(BackupManagerFactoryMock::class)
            ->run();

        $this->assertEquals('Something went wrong!', $result->getMessage());
        $this->assertEquals(1, $result->getExitCode());
    }
}
