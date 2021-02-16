<?php

namespace DigipolisGent\Robo\Task\Deploy\Ssh\Adapter;

use phpseclib3\Net\SSH2;
use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\AbstractAuth;

class SshPhpseclibAdapter implements SshAdapterInterface
{

    /**
     * The phpseclib ssh client.
     *
     * @var \phpseclib3\Net\SSH2
     */
    protected $ssh;

    /**
     * {@inheritdoc}
     */
    public function __construct($host, $port = 22, $timeout = 10)
    {
        $this->ssh = new SSH2($host, $port, $timeout);
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        return $this->ssh->_connect();
    }

    /**
     * {@inheritdoc}
     */
    public function disablePTY()
    {
        return $this->ssh->disablePTY();
    }

    /**
     * {@inheritdoc}
     */
    public function disableQuietMode()
    {
        return $this->ssh->disableQuietMode();
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        return $this->ssh->disconnect();
    }

    /**
     * {@inheritdoc}
     */
    public function enablePTY()
    {
        return $this->ssh->enablePTY();
    }

    /**
     * {@inheritdoc}
     */
    public function enableQuietMode()
    {
        return $this->ssh->enableQuietMode();
    }

    /**
     * {@inheritdoc}
     */
    public function exec($command, $callback = null)
    {
        return $this->ssh->exec($command, $callback);
    }

    /**
     * {@inheritdoc}
     */
    public function getBannerMessage()
    {
        return $this->ssh->getBannerMessage();
    }

    /**
     * {@inheritdoc}
     */
    public function getCompressionAlgorithmsClient2Server()
    {
        return $this->ssh->getCompressionAlgorithmsClient2Server();
    }

    /**
     * {@inheritdoc}
     */
    public function getCompressionAlgorithmsServer2Client()
    {
        return $this->ssh->getCompressionAlgorithmsServer2Client();
    }

    /**
     * {@inheritdoc}
     */
    public function getEncryptionAlgorithmsClient2Server()
    {
        return $this->ssh->getEncryptionAlgorithmsClient2Server();
    }

    /**
     * {@inheritdoc}
     */
    public function getEncryptionAlgorithmsServer2Client()
    {
        return $this->ssh->getEncryptionAlgorithmsServer2Client();
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        return $this->ssh->getErrors();
    }

    /**
     * {@inheritdoc}
     */
    public function getExitStatus()
    {
        return $this->ssh->getExitStatus();
    }

    /**
     * {@inheritdoc}
     */
    public function getKexAlgorithms()
    {
        return $this->ssh->getKexAlgorithms();
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguagesClient2Server()
    {
        return $this->ssh->getLanguagesClient2Server();
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguagesServer2Client()
    {
        return $this->ssh->getLanguagesServer2Client();
    }

    /**
     * {@inheritdoc}
     */
    public function getLastError()
    {
        return $this->ssh->getLastError();
    }

    /**
     * {@inheritdoc}
     */
    public function getLog()
    {
        return $this->ssh->getLog();
    }

    /**
     * {@inheritdoc}
     */
    public function getMACAlgorithmsClient2Server()
    {
        return $this->ssh->getMACAlgorithmsClient2Server();
    }

    /**
     * {@inheritdoc}
     */
    public function getMACAlgorithmsServer2Client()
    {
        return $this->ssh->getMACAlgorithmsServer2Client();
    }

    /**
     * {@inheritdoc}
     */
    public function getServerHostKeyAlgorithms()
    {
        return $this->ssh->getServerHostKeyAlgorithms();
    }

    /**
     * {@inheritdoc}
     */
    public function getServerIdentification()
    {
        return $this->ssh->getServerIdentification();
    }

    /**
     * {@inheritdoc}
     */
    public function getServerPublicHostKey()
    {
        return $this->ssh->getServerPublicHostKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getStdError()
    {
        return $this->ssh->getStdError();
    }

    /**
     * {@inheritdoc}
     */
    public function getWindowColumns()
    {
        return $this->ssh->getWindowColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function getWindowRows()
    {
        return $this->ssh->getWindowRows();
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthenticated()
    {
        return $this->ssh->isAuthenticated();
    }

    /**
     * {@inheritdoc}
     */
    public function isConnected()
    {
        return $this->ssh->isConnected();
    }

    /**
     * {@inheritdoc}
     */
    public function isPTYEnabled()
    {
        return $this->ssh->isPTYEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function isQuietModeEnabled()
    {
        return $this->ssh->isQuietModeEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function isTimeout()
    {
        return $this->ssh->isTimeout();
    }

    /**
     * {@inheritdoc}
     */
    public function login(AbstractAuth $auth)
    {
        $auth->authenticate($this->ssh);
    }

    /**
     * {@inheritdoc}
     */
    public function read($expect = '', $mode = self::READ_SIMPLE)
    {
        return $this->ssh->read($expect, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        return $this->ssh->reset();
    }

    /**
     * {@inheritdoc}
     */
    public function setCryptoEngine($engine)
    {
        return $this->ssh->setCryptoEngine($engine);
    }

    /**
     * {@inheritdoc}
     */
    public function setTimeout($timeout)
    {
        return $this->ssh->setTimeout($timeout);
    }

    /**
     * {@inheritdoc}
     */
    public function setWindowColumns($value)
    {
        return $this->ssh->setWindowColumns($value);
    }

    /**
     * {@inheritdoc}
     */
    public function setWindowRows($value)
    {
        return $this->ssh->setWindowRows($value);
    }

    /**
     * {@inheritdoc}
     */
    public function setWindowSize($columns = 80, $rows = 24)
    {
        return $this->ssh->setWindowSize($columns, $rows);
    }

    /**
     * {@inheritdoc}
     */
    public function startSubsystem($subsystem)
    {
        return $this->ssh->startSubsystem($subsystem);
    }

    /**
     * {@inheritdoc}
     */
    public function stopSubsystem()
    {
        return $this->ssh->stopSubsystem();
    }

    /**
     * {@inheritdoc}
     */
    public function write($cmd)
    {
        return $this->ssh->write($cmd);
    }
}
