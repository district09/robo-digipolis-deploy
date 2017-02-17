<?php

namespace DigipolisGent\Robo\Task\Deploy;

use DigipolisGent\Robo\Task\Deploy\Scp\Adapter\ScpAdapterInterface;
use DigipolisGent\Robo\Task\Deploy\Scp\Factory\ScpFactoryInterface;
use DigipolisGent\Robo\Task\Deploy\Scp\Factory\ScpPhpseclibFactory;
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
    use \Robo\TaskAccessor;

    /**
     * The server to scp to.
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
     * The fully qualified classname of the scp factory.
     *
     * @var string
     */
    protected $scpFactory = ScpPhpseclibFactory::class;


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
     * Set the ScpFactory.
     *
     * @param string|ScpFactoryInterface $class
     *   A factory instance or the fully qualified classname of the scp factory.
     *   The given class (whether it's a classname or instance) must implement
     *   \DigipolisGent\Robo\Task\Deploy\Scp\Factory\ScpFactoryInterface.
     *
     * @throws InvalidArgumentException
     *   If the class is not an instance of
     *   \DigipolisGent\Robo\Task\Deploy\Scp\Factory\ScpFactoryInterface.
     *
     * @return $this
     */
    public function scpFactory($class)
    {
        if (!is_subclass_of($class, ScpFactoryInterface::class)) {
            throw new InvalidArgumentException(sprintf(
                'SCP Factory %s does not implement %s.',
                $class,
                ScpFactoryInterface::class
            ));
        }
        $this->scpFactory = $class;

        return $this;
    }

    /**
     * Set the SshFactory.
     *
     * @param string|\DigipolisGent\Robo\Task\Deploy\Ssh\Factory\SshFactoryInterface $class
     *   A factory instance or the fully qualified classname of the scp factory.
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

        $ssh = call_user_func([$this->sshFactory, 'create'], $this->host, $this->port, $this->timeout);
        $ssh->login($this->auth);
        $mkdir = 'mkdir -p ' . $this->destinationFolder;
        $mkdirResult = $ssh->exec($mkdir);
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
        $scp = call_user_func([$this->scpFactory, 'create'], $this->host, $this->auth, $this->port, $this->timeout);
        $uploadResult = $scp->put(
            $this->destinationFolder . DIRECTORY_SEPARATOR . basename($this->package),
            $this->package,
            ScpAdapterInterface::SOURCE_LOCAL_FILE
        );
        if (!$uploadResult) {
            $errorMessage = sprintf(
                'Could not %s file %s on %s on port %s to directory %dir',
                'upload',
                $this->package,
                $this->host,
                $this->port,
                $this->destinationFolder
            );
            return Result::error($this, $errorMessage);
        }
        $untar = 'cd ' . $this->destinationFolder .
            ' && tar -xzf ' . basename($this->package) .
            ' && rm -rf ' . basename($this->package);
        $ssh->exec($untar);
        $untarResult = $ssh->exec($untar);
        if ($untarResult !== false && $ssh->getExitStatus() !== 0) {
            $errorMessage = sprintf(
                'Could not execute %s on %s on port %s with message: %s',
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
