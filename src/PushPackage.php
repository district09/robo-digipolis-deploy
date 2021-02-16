<?php

namespace DigipolisGent\Robo\Task\Deploy;

use DigipolisGent\Robo\Task\Deploy\SFTP\Adapter\SFTPAdapterInterface;
use DigipolisGent\Robo\Task\Deploy\SFTP\Factory\SFTPFactoryInterface;
use DigipolisGent\Robo\Task\Deploy\SFTP\Factory\SFTPPhpseclibFactory;
use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\AbstractAuth;
use DigipolisGent\Robo\Task\Deploy\Ssh\Factory\SshFactoryInterface;
use DigipolisGent\Robo\Task\Deploy\Ssh\Factory\SshPhpseclibFactory;
use InvalidArgumentException;
use Robo\Result;
use Robo\Task\BaseTask;

class PushPackage extends BaseTask
{
    use loadTasks;
    use \Robo\Task\Remote\loadTasks;

    /**
     * The server to sftp to.
     *
     * @var string
     */
    protected $host;

    /**
     * The ssh port of the server. Defaults to 22.
     *
     * @var int
     */
    protected $port = 22;

    /**
     * The ssh timeout. Defaults to 10 seconds.
     *
     * @var int
     */
    protected $timeout = 10;

    /**
     * The source package to push to the server.
     *
     * @var string
     */
    protected $package;

    /**
     * The destination folder in which to unzip the package.
     *
     * @var string
     */
    protected $destinationFolder;

    /**
     * The authentication for the ssh connection.
     *
     * @var AbstractAuth
     */
    protected $auth;

    /**
     * The fully qualified classname of the sftp factory.
     *
     * @var string
     */
    protected $SFTPFactory = SFTPPhpseclibFactory::class;


    /**
     * The fully qualified classname of the ssh factory.
     *
     * @var string
     */
    protected $sshFactory = SshPhpseclibFactory::class;

    /**
     * Creates a new PushPackage task.
     *
     * @param string $host
     *   The host.
     * @param AbstractAuth $auth
     *   Authentication data.
     */
    public function __construct($host, AbstractAuth $auth)
    {
        $this->host = $host;
        $this->auth = $auth;
    }

    /**
     * Sets the port to connect on.
     *
     * @param int $port
     *   The port to connect on.
     *
     * @return $this
     */
    public function port($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Sets the ssh timeout.
     *
     * @param int $timeout
     *   Timeout in seconds.
     *
     * @return $this
     */
    public function timeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Sets the package path.
     *
     * @param string $path
     *   The package path.
     *
     * @return $this
     */
    public function package($path)
    {
        $this->package = $path;

        return $this;
    }

    /**
     * Sets the destination folder in which to unzip the package on the server.
     *
     * @param string $destinationFolder
     *   The destination folder.
     *
     * @return $this
     */
    public function destinationFolder($destinationFolder)
    {
        $this->destinationFolder = $destinationFolder;

        return $this;
    }

    /**
     * Set the SFTPFactory.
     *
     * @param string|SFTPFactoryInterface $class
     *   A factory instance or the fully qualified classname of the sftp factory.
     *   The given class (whether it's a classname or instance) must implement
     *   \DigipolisGent\Robo\Task\Deploy\SFTP\Factory\SFTPFactoryInterface.
     *
     * @throws InvalidArgumentException
     *   If the class is not an instance of
     *   \DigipolisGent\Robo\Task\Deploy\SFTP\Factory\SFTPFactoryInterface.
     *
     * @return $this
     */
    public function SFTPFactory($class)
    {
        if (!is_subclass_of($class, SFTPFactoryInterface::class)) {
            throw new InvalidArgumentException(sprintf(
                'SFTP Factory %s does not implement %s.',
                $class,
                SFTPFactoryInterface::class
            ));
        }
        $this->SFTPFactory = $class;

        return $this;
    }

    /**
     * Set the SshFactory.
     *
     * @param string|\DigipolisGent\Robo\Task\Deploy\Ssh\Factory\SshFactoryInterface $class
     *   A factory instance or the fully qualified classname of the ssh factory.
     *   The given class (whether it's a classname or instance) must implement
     *   \DigipolisGent\Robo\Task\Deploy\Ssh\Factory\SshFactoryInterface.
     *
     * @throws InvalidArgumentException
     *   If the class is not an instance of
     *   \DigipolisGent\Robo\Task\Deploy\Ssh\Factory\SshFactoryInterface.
     *
     * @return $this
     */
    public function sshFactory($class)
    {
        if (!is_subclass_of($class, SshFactoryInterface::class)) {
            throw new InvalidArgumentException(sprintf(
                'SSH Factory %s does not implement %s.',
                $class,
                SshFactoryInterface::class
            ));
        }
        $this->sshFactory = $class;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {

        $ssh = call_user_func(
            [$this->sshFactory, 'create'],
            $this->host,
            $this->port,
            $this->timeout
        );
        $this->printTaskInfo(sprintf(
            'Establishing SSH connection to %s on port %s.',
            $this->host,
            $this->port
        ));
        $ssh->login($this->auth);
        $mkdir = 'mkdir -p ' . $this->destinationFolder;
        $this->printTaskInfo(sprintf(
            '%s@%s:~$ %s',
            $this->auth->getUser(),
            $this->host,
            $mkdir
        ));
        $mkdirResult = $ssh->exec($mkdir, [$this, 'printTaskInfo']);
        if ($mkdirResult !== false && $ssh->getExitStatus() !== 0) {
            $errorMessage = sprintf(
                'Could not execute %s on %s on port %s with message: %s',
                $mkdir,
                $this->host,
                $this->port,
                $ssh->getStdError()
            );
            return Result::error($this, $errorMessage);
        }
        $sftp = call_user_func(
            [$this->SFTPFactory, 'create'],
            $this->host,
            $this->auth,
            $this->port,
            $this->timeout
        );
        $this->printTaskInfo(sprintf(
            'Uploading file %s on %s on port %s to directory %s.',
            $this->package,
            $this->host,
            $this->port,
            $this->destinationFolder
        ));
        $uploadResult = $sftp->put(
            $this->destinationFolder . DIRECTORY_SEPARATOR . basename($this->package),
            $this->package,
            SFTPAdapterInterface::SOURCE_LOCAL_FILE
        );
        if (!$uploadResult) {
            $errorMessage = sprintf(
                'Could not %s file %s on %s on port %s to directory %s.',
                'upload',
                $this->package,
                $this->host,
                $this->port,
                $this->destinationFolder
            );
            return Result::error($this, $errorMessage);
        }
        $untar = 'cd ' . $this->destinationFolder .
            ' && tar -xzvf ' . basename($this->package) .
            ' && rm -rf ' . basename($this->package);
        $this->printTaskInfo(sprintf(
            '%s@%s:~$ %s',
            $this->auth->getUser(),
            $this->host,
            $untar
        ));
        $untarResult = $ssh->exec($untar, [$this, 'printTaskInfo']);
        if ($untarResult === false || $ssh->getExitStatus() !== 0) {
            $errorMessage = sprintf(
                'Could not execute %s on %s on port %s with message: %s.',
                $untar,
                $this->host,
                $this->port,
                $ssh->getStdError()
            );
            return Result::error($this, $errorMessage);
        }
        return Result::success($this);
    }
}
