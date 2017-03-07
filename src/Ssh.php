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
     * Whether or not to use the physical directory structure without following
     * symbolic links.
     *
     * @var bool
     */
    protected $physicalRemoteDir = false;

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
            'command' => $this->receiveCommand($command),
            'callback' => $callback,
        ];

        return $this;
    }

    /**
     * Sets the remote directory.
     *
     * @param string $directory
     *   The remote directory.
     * @param bool $physical
     *   Use the physical directory structure without following symbolic links
     *   (-P argument for cd).
     *
     * @return $this
     */
    public function remoteDirectory($directory, $physical = false)
    {
        $this->remoteDir = $directory;
        $this->physicalRemoteDir = $physical;

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
        $ssh = call_user_func(
            [$this->sshFactory, 'create'],
            $this->host,
            $this->port,
            $this->timeout
        );
        $this->startTimer();
        $ssh->login($this->auth);
        $errorMessage = '';
        $cd = '';
        if ($this->remoteDir) {
            $opt = $this->physicalRemoteDir ? '-P ': '';
            $cd = 'cd ' . $opt . $this->remoteDir . ' && ';
        }
        foreach ($this->commandStack as $command) {
            $this->printTaskInfo(sprintf(
                '%s@%s:%s$ %s',
                $this->auth->getUser(),
                $this->host,
                $this->remoteDir,
                $command['command']
            ));
            $result = call_user_func_array(
                [
                    $ssh,
                    $command['method'],
                ],
                [
                    $cd . $command['command'],
                    $this->commandCallback($command['callback']),
                ]
            );
            if ($result === false || $ssh->getExitStatus() !== 0) {
                $errorMessage .= sprintf(
                    'Could not execute %s on %s on port %s in folder %s with message: %s.',
                    $cd . $command['command'],
                    $this->host,
                    $this->port,
                    $this->remoteDir,
                    $ssh->getStdError()
                );
                if ($ssh->isTimeout()) {
                    $errorMessage .= ' ';
                    $errorMessage .= sprintf(
                        'Connection timed out. Execution took %s, timeout is set at %s seconds.',
                        $this->getExecutionTime(),
                        $this->timeout
                    );
                }
                if ($this->stopOnFail) {
                    return Result::error($this, $errorMessage);
                }
                $errorMessage .= "\n";
            }
        }
        $this->stopTimer();
        return $errorMessage
            ? Result::error($this, $errorMessage)
            : Result::success($this);
    }

    /**
     * Wrap the callback so we can print the output.
     *
     * @param callable $callback
     *   The callback to wrap.
     */
    protected function commandCallback($callback)
    {
        return (
            function ($output) use ($callback) {
                $this->printTaskInfo($output);
                if (is_callable($callback)) {
                    return call_user_func($callback, $output);
                }
            }
        );
    }
}
