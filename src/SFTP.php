<?php

namespace DigipolisGent\Robo\Task\Deploy;

use DigipolisGent\Robo\Task\Deploy\SFTP\Adapter\SFTPAdapterInterface;
use DigipolisGent\Robo\Task\Deploy\SFTP\Factory\SFTPFactoryInterface;
use DigipolisGent\Robo\Task\Deploy\SFTP\Factory\SFTPPhpseclibFactory;
use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\AbstractAuth;
use InvalidArgumentException;
use Robo\Result;
use Robo\Task\BaseTask;

class SFTP extends BaseTask
{
    /**
     * The server to SFTP to/from.
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
     * The fully qualified classname of the sftp factory.
     *
     * @var string
     */
    protected $SFTPFactory = SFTPPhpseclibFactory::class;

    /**
     * Creates a new SFTP task.
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
     * Downloads a file from the SFTP server.
     *
     * @param string $remoteFile
     *   The path to the remote file on the sftp server.
     * @param string $localFile
     *   The path to download the local file to.
     *
     * @return $this
     */
    public function get($remoteFile, $localFile)
    {
        $this->commandStack[] = [
            'method' => 'get',
            'arguments' => [
                $remoteFile,
                $localFile,
            ],
        ];

        return $this;
    }

    /**
     * Uploads a file to the SFTP server.
     *
     * By default, we assume $data is a filename. This means $remote_file will
     * contain as many bytes as the fileon your local filesystem.  If the file
     * is 1MB then that is how large $remote_file will be, as well.
     *
     * Setting $mode to SFTPAdapterInterface::SOURCE_STRING will change the above
     * behavior. With SFTPAdapterInterface::SOURCE_STRING, we do not read from
     * the local filesystem.  $data is dumped directly into $remote_file. So,
     * for example, if you set $data to 'filename.ext' and set $mode to
     * \phpseclib3\Net\SFTP::SOURCE_STRING then you will upload a file, twelve
     * bytes long, containing 'filename.ext' as its contents.
     *
     * Currently, only binary mode is supported.  As such, if the line endings
     * need to be adjusted, you will need to take care of that, yourself.
     *
     * @param string $remoteFile
     *   The path on the SFTP server to put the file.
     * @param string $data
     *   The data or local filename of the file containing the data for the
     *   remote file.
     * @param int $mode
     *   One of SFTPAdapterInterface::SOURCE_LOCAL_FILE or
     *   SFTPAdapterInterface::SOURCE_STRING.
     * @param null|callable $callback
     *   A function to call each time a chunk of the file has been uploaded. The
     *   function takes one parameter: the size of the data that has already
     *   been uploaded.
     *
     * @return $this
     */
    public function put($remoteFile, $data, $mode = SFTPAdapterInterface::SOURCE_LOCAL_FILE, $callback = null)
    {
        $this->commandStack[] = [
            'method' => 'put',
            'arguments' => [
                  $remoteFile,
                  $data,
                  $mode,
                  $callback,
            ],
        ];

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
     * Set the SFTPFactory.
     *
     * @param string|\DigipolisGent\Robo\Task\Deploy\SFTP\Factory\SFTPFactoryInterface $class
     *   A factory instance or the fully qualified classname of the SFTP factory.
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
     * {@inheritdoc}
     */
    public function run()
    {
        $sftp = call_user_func([$this->SFTPFactory, 'create'], $this->host, $this->auth, $this->port, $this->timeout);
        $errorMessage = '';
        foreach ($this->commandStack as $command) {
            $this->printTaskInfo(sprintf(
                'Executing SFTP method %s with arguments %s.',
                $command['method'],
                implode(',', array_map(
                    function ($v) {
                        return print_r($v, true);
                    },
                    $command['arguments']
                ))
            ));
            $result = call_user_func_array([$sftp, $command['method']], $command['arguments']);
            if (!$result) {
                $errorMessage .= sprintf(
                    'Could not %s file %s on %s on port %s',
                    $command['method'],
                    reset($command['arguments']),
                    $this->host,
                    $this->port
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
