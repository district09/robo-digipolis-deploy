<?php

namespace DigipolisGent\Robo\Task\Deploy\SFTP\Adapter;

interface SFTPAdapterInterface
{

    const SOURCE_LOCAL_FILE = 1;
    const SOURCE_STRING = 2;

    /**
     * Downloads a file from the SFTP server.
     *
     * @param string $remoteFile
     *   The path to the remote file on the sftp server.
     * @param string $localFile
     *   The path to download the local file to.
     *
     * @return bool
     *   True on success, false on failure.
     */
    public function get($remoteFile, $localFile);

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
     *   The path on the sftp server to put the file.
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
     * @return bool
     *   True on success, false on failure.
     */
    public function put($remoteFile, $data, $mode = self::SOURCE_LOCAL_FILE, $callback = null);
}
