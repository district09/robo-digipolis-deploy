<?php

namespace DigipolisGent\Robo\Task\Deploy;

use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\AbstractAuth;
use DigipolisGent\Robo\Task\Deploy\Ssh\Factory\SshPhpseclibFactory;
use DigipolisGent\Robo\Task\Deploy\Ssh\Factory\SshFactoryInterface;
use Robo\Result;
use Robo\Task\BaseTask;

class Ssh extends BaseTask
{
    use \Robo\Common\CommandReceiver;

    /**
     * The server to scp to/from.
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
     * The command stack to execute.
     *
     * @var array
     */
    protected $commandStack = [];

    /**
     * Whether or not to stop on fail.
     *
     * @var bool
     */
    protected $stopOnFail = false;

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
    protected $sshFactory = SshPhpseclibFactory::class;

    /**
     * The remote directory to execute the commands in.
     *
     * @var string
     */
    protected $remoteDir;

    /**
     * Creates a new Ssh task.
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
     * @param string|CommandInterface $command
     * @param callable $callback
     *
     * @return $this
     */
    public function exec($command, $callback = null)
    {
        $this->commandStack[] = [
            'method' => 'exec',
            'arguments' => [$this->receiveCommand($command), $callback]
        ];

        return $this;
    }

    /**
     * Sets the remote directory.
     *
     * @param string $directory
     *   The remote directory.
     *
     * @return $this
     */
    public function remoteDirectory($directory)
    {
        $this->remoteDir = $directory;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function progressIndicatorSteps()
    {
        return count($this->commandStack);
    }

    /**
     * Should we stop up- or downloading files once one has failed?
     *
     * @param bool $stopOnFail
     *   Whether or not we should stop on fail.
     *
     * @return $this
     */
    public function stopOnFail($stopOnFail = true)
    {
        $this->stopOnFail = $stopOnFail;

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
            throw new \InvalidArgumentException(sprintf(
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
        $errorMessage = '';
        if ($this->remoteDir && !$ssh->exec('cd ' . $this->remoteDir)) {
            return Result::error($this, 'Could not change to remote directory ' . $this->remoteDir);
        }
        foreach ($this->commandStack as $command) {
            $this->printTaskInfo(sprintf(
                'Executing SSH method %s with arguments %s.',
                $command['method'],
                implode(',', array_map(
                    function($v)
                    {
                        return print_r($v, true);
                    },
                    $command['arguments']
                ))
            ));
            $result = call_user_func_array([$ssh, $command['method']], $command['arguments']);
            if (!$result) {
                $errorMessage .= sprintf(
                    'Could not execute %s on %s on port %s with message: %s',
                    reset($command['arguments']),
                    $this->host,
                    $this->port,
                    $ssh->getStdError()
                );
                if ($this->stopOnFail) {
                    return Result::error($this, $errorMessage);
                }
                $errorMessage .= "\n";
            }
        }
        return $errorMessage
            ? Result::error($this, $errorMessage)
            : Result::success($this);
    }
}
