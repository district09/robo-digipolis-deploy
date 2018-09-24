<?php

namespace DigipolisGent\Robo\Task\Deploy\Scp\Net;

use phpseclib\Net\SCP as SeclibSCP;

class SCP extends SeclibSCP
{

    /**
     * Downloads a file from the SCP server.
     *
     * Returns a string containing the contents of $remote_file if $local_file
     * is left undefined or a boolean false if the operation was unsuccessful.
     * If $local_file is defined, returns true or false depending on the success
     * of the operation.
     *
     * This method is overridden to fix
     * https://github.com/phpseclib/phpseclib/issues/146.
     *
     * @param string $remote_file
     * @param string $local_file
     * @return mixed
     * @access public
     */
    function get($remote_file, $local_file = false)
    {
        if (!isset($this->ssh)) {
            return false;
        }

        if (!$this->ssh->exec('scp -f ' . escapeshellarg($remote_file), false)) { // -f = from
            return false;
        }

        $this->_send("\0");

        if (!preg_match('#(?<perms>[^ ]+) (?<size>\d+) (?<name>.+)#', rtrim($this->_receive()), $info)) {
            return false;
        }

        $this->_send("\0");

        $size = 0;

        if ($local_file !== false) {
            $fp = @fopen($local_file, 'wb');
            if (!$fp) {
                return false;
            }
        }

        $content = '';
        while ($size < $info['size']) {
            $data = $this->_receive();

            // This if statement is the fix for
            // https://github.com/phpseclib/phpseclib/issues/146.
            if (strlen($data) + $size > $info['size']) {
                $data = substr($data, 0, $info['size'] - $size);
            }

            // SCP usually seems to split stuff out into 16k chunks
            $size+= strlen($data);

            if ($local_file === false) {
                $content.= $data;
            } else {
                fputs($fp, $data);
            }
        }

        $this->_close();

        if ($local_file !== false) {
            fclose($fp);
            return true;
        }

        return $content;
    }

}
