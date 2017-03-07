<?php

namespace DigipolisGent\Robo\Task\Deploy;

use Robo\Contract\CommandInterface;
use Robo\Result;
use Robo\Task\BaseTask;

class ClearOpCache extends BaseTask implements CommandInterface
{
    use \Robo\Common\ExecCommand;

    const ENV_FCGI = 'fcgi';
    const ENV_CLI = 'cli';

    /**
     * The host type (cli or fcgi).
     *
     * @var string
     */
    protected $environment;

    /**
     * The fcgi host (if the environment is fcgi).
     *
     * @var string
     */
    protected $host;

    /**
     * Creates a new ClearOpCache task.
     *
     * @param string $environment
     *   One of the ClearOpCache::ENV_* constants.
     * @param string $host
     *   If the environment is FCGI, the host (path to socket or ip:port).
     */
    public function __construct($environment = self::ENV_FCGI, $host = null)
    {
        $this->environment = $environment;
        $this->host = $host;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (!class_exists('\\CacheTool\\CacheTool')) {
            return Result::errorMissingPackage($this, '\\CacheTool\\CacheTool', 'gordalina/cachetool');
        }

        $adapter = null;
        switch ($this->environment) {
            case static::ENV_CLI:
                $adapter = new \CacheTool\Adapter\Cli();
                break;

            case static::ENV_FCGI:
            default:
                $adapter = new \CacheTool\Adapter\FastCGI($this->host);
                break;
        }
        $this->printTaskInfo(sprintf('Resetting opcache for %s.', $this->environment));
        $cachetool = \CacheTool\CacheTool::factory($adapter);
        $cachetool->opcache_reset();
        return Result::success($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand()
    {
        $tool = $this->findExecutable('cachetool');
        $cmd = $tool . ' opcache:reset --' . $this->environment;
        if ($this->environment === static::ENV_FCGI && $this->host) {
            $cmd .= '=' . $this->host;
        }
        return $cmd;
    }
}
