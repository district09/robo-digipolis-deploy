<?php

namespace DigipolisGent\Robo\Task\Deploy\SFTP\Adapter;

use phpseclib3\Net\SFTP;

class SFTPPhpseclibAdapter implements SFTPAdapterInterface
{

    /**
     * The phpseclib sftp client.
     *
     * @var \phpseclib3\Net\SFTP
     */
    protected $sftp;

    public function __construct(SFTP $sftp)
    {
        $this->sftp = $sftp;
    }

    /**
     * {@inheritdoc}
     */
    public function get($remoteFile, $localFile)
    {
        return $this->sftp->get($remoteFile, $localFile);
    }

    /**
     * {@inheritdoc}
     */
    public function put($remoteFile, $data, $mode = self::SOURCE_LOCAL_FILE, $callback = null)
    {
        $seclibMode = SFTP::SOURCE_LOCAL_FILE;
        if ($mode === static::SOURCE_STRING) {
            $seclibMode = SFTP::SOURCE_STRING;
        }
        return $this->sftp->put($remoteFile, $data, $seclibMode, $callback);
    }
}
