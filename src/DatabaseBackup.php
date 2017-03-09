<?php

namespace DigipolisGent\Robo\Task\Deploy;

use BackupManager\Filesystems\Destination;
use DigipolisGent\Robo\Task\Deploy\BackupManager\Factory\BackupManagerFactory;
use DigipolisGent\Robo\Task\Deploy\BackupManager\Factory\BackupManagerFactoryInterface;
use Robo\Result;
use Robo\Task\BaseTask;

class DatabaseBackup extends BaseTask
{
    /**
     * Config for storing the backup.
     *
     * @var string|array
     */
    protected $filesystemConfig;

    /**
     * Config for the databases.
     *
     * @var string|array
     */
    protected $dbConfig;

    /**
     * The name of the database (from config).
     *
     * @var string
     */
    protected $database;

    /**
     * The destinations to backup to.
     *
     * @var \BackupManager\Filesystems\Destination[]
     */
    protected $destinations;

    /**
     * The compression to use for the backup.
     *
     * @var string
     */
    protected $compression = 'null';

    /**
     * The fully qualified classname of the backup manager factory.
     *
     * @var string|BackupManagerFactoryInterface
     */
    protected $backupManagerFactory = BackupManagerFactory::class;

    /**
     * Creates a BackupManagerAdapter.
     *
     * @param string|array $filesystemConfig
     *   Config for the FilesystemProvider. A path to a PHP file or an array.
     * @param string|array $dbConfig
     *   Config for the DatabaseProvider. A path to a PHP file or an array.
     *
     * @return \DigipolisGent\Robo\Task\Deploy\BackupManager\Adapter\BackupManagerAdapterInterface
     */
    public function __construct($filesystemConfig, $dbConfig)
    {
        $this->filesystemConfig = $filesystemConfig;
        $this->dbConfig = $dbConfig;
    }

    /**
     * Set the BackupManagerFactory.
     *
     * @param string|BackupManagerFactoryInterface $class
     *   A factory instance or the fully qualified classname of the backup
     *   manager factory. The given class (whether it's a classname or instance)
     *   must implement BackupManagerFactoryInterface.
     *
     * @throws \InvalidArgumentException
     *   If the class is not an instance of BackupManagerFactoryInterface.
     *
     * @return $this
     */
    public function backupManagerFactory($class)
    {
        if (!is_subclass_of($class, BackupManagerFactoryInterface::class)) {
            throw new \InvalidArgumentException(sprintf(
                'Backup Manager Factory %s does not implement %s.',
                $class,
                BackupManagerFactoryInterface::class
            ));
        }
        $this->backupManagerFactory = $class;

        return $this;
    }

    /**
     * The database name to backup.
     *
     * @param string $database
     *   The database name as defined in config.
     *
     * @return $this
     */
    public function database($database)
    {
        $this->database = $database;

        return $this;
    }

    /**
     * Add a destination to backup to.
     *
     * @param string $destination
     *   The path to backup to.
     * @param string $destinationType
     *   The destination type to backup to. Defaults to 'local'.
     *
     * @return $this
     */
    public function destination($destination, $destinationType = 'local')
    {
        $this->destinations[] = new Destination($destinationType, $destination);

        return $this;
    }

    /**
     * Set the compression for the backup.
     *
     * @param string $compression
     *   The compression for the backup.
     *
     * @return $this
     */
    public function compression($compression)
    {
        $this->compression = $compression;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        try {
            /**
             * The backup manager
             * @var \DigipolisGent\Robo\Task\Deploy\BackupManager\Adapter\BackupManagerAdapterInterface
             */
            $manager = call_user_func(
                [$this->backupManagerFactory, 'create'],
                $this->filesystemConfig,
                $this->dbConfig
            );
            if (empty($this->destinations)) {
                $this->destination(getcwd());
            }
            $this->printTaskInfo(sprintf(
                'Creating database backup with %s compression.',
                $this->compression
            ));
            $manager->makeBackup()->run(
                $this->database,
                $this->destinations,
                $this->compression
            );
        } catch (\Exception $e) {
            return Result::fromException($this, $e);
        }
        return Result::success($this);
    }
}
