<?php

namespace DigipolisGent\Robo\Task\Deploy\Scp\Adapter;

use phpseclib\Net\SCP;

class ScpPhpseclibAdapter implements ScpAdapterInterface
{

    /**
     * The phpseclib scp client.
     *
     * @var \phpseclib\Net\SCP
     */
    protected $scp;

    public function __construct(SCP $scp)
    {
        $this->scp = $scp;
    }

    /**
     * {@inheritdoc}
     */
    public function get($remoteFile, $localFile)
    {
        return $this->scp->get($remoteFile, $localFile);
    }

    /**
     * {@inheritdoc}
     */
    public function put($remoteFile, $data, $mode = self::SOURCE_LOCAL_FILE, $callback = null)
    {
        $seclibMode = SCP::SOURCE_LOCAL_FILE;
        if ($mode === static::SOURCE_STRING) {
            $seclibMode = SCP::SOURCE_STRING;
        }
        return $this->scp->put($remoteFile, $data, $seclibMode, $callback);
    }

}
