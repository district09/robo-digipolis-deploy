<?php

namespace DigipolisGent\Robo\Task\Deploy\Ssh\Adapter;

use DigipolisGent\Robo\Task\Deploy\Ssh\Auth\AbstractAuth;
use phpseclib3\Net\SSH2;

interface SshAdapterInterface
{
    /**
     * Execution Bitmap Masks
     *
     * @see SSH2::bitmap
     * @access private
     */
    const MASK_CONSTRUCTOR   = 0x00000001;
    const MASK_CONNECTED     = 0x00000002;
    const MASK_LOGIN_REQ     = 0x00000004;
    const MASK_LOGIN         = 0x00000008;
    const MASK_SHELL         = 0x00000010;
    const MASK_WINDOW_ADJUST = 0x00000020;

    /**
     * Channel constants
     *
     * RFC4254 refers not to client and server channels but rather to sender and
     * recipient channels. We don't refer to them in that way because RFC4254
     * toggles the meaning. the client sends a SSH_MSG_CHANNEL_OPEN message with
     * a sender channel and the server sends a SSH_MSG_CHANNEL_OPEN_CONFIRMATION
     * in response, with a sender and a recepient channel.  at first glance, you
     * might conclude that SSH_MSG_CHANNEL_OPEN_CONFIRMATION's sender channel
     * would be the same thing as SSH_MSG_CHANNEL_OPEN's sender channel, but
     * it's not, per this snipet:
     *     The 'recipient channel' is the channel number given in the original
     *     open request, and 'sender channel' is the channel number allocated by
     *     the other side.
     *
     * @see SSH2::_send_channel_packet()
     * @see SSH2::_get_channel_packet()
     * @access private
    */
    const CHANNEL_EXEC          = 0; // PuTTy uses 0x100
    const CHANNEL_SHELL         = 1;
    const CHANNEL_SUBSYSTEM     = 2;
    const CHANNEL_AGENT_FORWARD = 3;

    /**
     * @access public
     * @see SSH2::getLog()
    */
    /**
     * Returns the message numbers
     */
    const LOG_SIMPLE = 1;
    /**
     * Returns the message content
     */
    const LOG_COMPLEX = 2;
    /**
     * Outputs the content real-time
     */
    const LOG_REALTIME = 3;
    /**
     * Dumps the content real-time to a file
     */
    const LOG_REALTIME_FILE = 4;

    /**
     * @access public
     * @see SSH2::read()
    */
    /**
     * Returns when a string matching $expect exactly is found
     */
    const READ_SIMPLE = 1;
    /**
     * Returns when a string matching the regular expression $expect is found
     */
    const READ_REGEX = 2;
    /**
     * Make sure that the log never gets larger than this
     */
    const LOG_MAX_SIZE = 1048576; // 1024 * 1024


    /**
     * Default Constructor.
     *
     * $host can either be a string, representing the host or a stream resource.
     *
     * @param mixed $host
     * @param int $port
     * @param int $timeout
     * @see self::login()
     * @return SSH2
     * @access public
     */
    public function __construct($host, $port = 22, $timeout = 10);

    /**
     * Set Crypto Engine Mode
     *
     * Possible $engine values:
     * CRYPT_MODE_INTERNAL, CRYPT_MODE_MCRYPT
     *
     * @param int $engine
     * @access private
     */
    public function setCryptoEngine($engine);

    /**
     * Connect to an SSHv2 server
     *
     * @return bool
     * @access private
     */
    public function connect();

    /**
     * Login.
     *
     * @param AbstractAuth $auth
     *   Credentials
     */
    public function login(AbstractAuth $auth);

    /**
     * Set Timeout
     *
     * $ssh->exec('ping 127.0.0.1'); on a Linux host will never return and will
     * run indefinitely.  setTimeout() makes it so it'll timeout. Setting
     * $timeout to false or 0 will mean there is no timeout.
     *
     * @param mixed $timeout
     * @access public
     */
    public function setTimeout($timeout);

    /**
     * Get the output from stdError
     *
     * @access public
     */
    public function getStdError();

    /**
     * Execute Command
     *
     * If $callback is set to false then
     * \phpseclib3\Net\SSH2::_get_channel_packet(self::CHANNEL_EXEC) will need to
     * be called manually. In all likelihood, this is not a feature you want to
     * be taking advantage of.
     *
     * @param string $command
     * @param Callback $callback
     * @return string
     * @access public
     */
    public function exec($command, $callback = null);

    /**
     * Returns the output of an interactive shell
     *
     * Returns when there's a match for $expect, which can take the form of a
     * string literal or, if $mode == self::READ_REGEX, a regular expression.
     *
     * @see self::write()
     * @param string $expect
     * @param int $mode
     * @return string
     * @access public
     */
    public function read($expect = '', $mode = self::READ_SIMPLE);

    /**
     * Inputs a command into an interactive shell.
     *
     * @see self::read()
     * @param string $cmd
     * @return bool
     * @access public
     */
    public function write($cmd);

    /**
     * Start a subsystem.
     *
     * Right now only one subsystem at a time is supported. To support multiple
     * subsystem's stopSubsystem() could accept a string that contained the name
     * of the subsystem, but at that point, only one subsystem of each type
     * could be opened. To support multiple subsystem's of the same name maybe
     * it'd be best if startSubsystem() generated a new channel id and returns
     * that and then that that was passed into stopSubsystem() but that'll be
     * saved for a future date and implemented if there's sufficient demand for
     * such a feature.
     *
     * @see self::stopSubsystem()
     * @param string $subsystem
     * @return bool
     * @access public
     */
    public function startSubsystem($subsystem);

    /**
     * Stops a subsystem.
     *
     * @see self::startSubsystem()
     * @return bool
     * @access public
     */
    public function stopSubsystem();

    /**
     * Closes a channel
     *
     * If read() timed out you might want to just close the channel and have it
     * auto-restart on the next read() call
     *
     * @access public
     */
    public function reset();

    /**
     * Is timeout?
     *
     * Did exec() or read() return because they timed out or because they
     * encountered the end?
     *
     * @access public
     */
    public function isTimeout();

    /**
     * Disconnect
     *
     * @access public
     */
    public function disconnect();

    /**
     * Is the connection still active?
     *
     * @return bool
     * @access public
     */
    public function isConnected();

    /**
     * Have you successfully been logged in?
     *
     * @return bool
     * @access public
     */
    public function isAuthenticated();

    /**
     * Enable Quiet Mode
     *
     * Suppress stderr from output
     *
     * @access public
     */
    public function enableQuietMode();

    /**
     * Disable Quiet Mode
     *
     * Show stderr in output
     *
     * @access public
     */
    public function disableQuietMode();

    /**
     * Returns whether Quiet Mode is enabled or not
     *
     * @see self::enableQuietMode()
     * @see self::disableQuietMode()
     * @access public
     * @return bool
     */
    public function isQuietModeEnabled();

    /**
     * Enable request-pty when using exec()
     *
     * @access public
     */
    public function enablePTY();

    /**
     * Disable request-pty when using exec()
     *
     * @access public
     */
    public function disablePTY();

    /**
     * Returns whether request-pty is enabled or not
     *
     * @see self::enablePTY()
     * @see self::disablePTY()
     * @access public
     * @return bool
     */
    public function isPTYEnabled();

    /**
     * Returns a log of the packets that have been sent and received.
     *
     * Returns a string if NET_SSH2_LOGGING == self::LOG_COMPLEX, an array if
     * NET_SSH2_LOGGING == self::LOG_SIMPLE and false if
     * !defined('NET_SSH2_LOGGING')
     *
     * @access public
     * @return array|false|string
     */
    public function getLog();

    /**
     * Returns all errors
     *
     * @return string[]
     * @access public
     */
    public function getErrors();

    /**
     * Returns the last error
     *
     * @return string
     * @access public
     */
    public function getLastError();

    /**
     * Return the server identification.
     *
     * @return string
     * @access public
     */
    public function getServerIdentification();

    /**
     * Return a list of the key exchange algorithms the server supports.
     *
     * @return array
     * @access public
     */
    public function getKexAlgorithms();

    /**
     * Return a list of the host key (public key) algorithms the server
     * supports.
     *
     * @return array
     * @access public
     */
    public function getServerHostKeyAlgorithms();

    /**
     * Return a list of the (symmetric key) encryption algorithms the server
     * supports, when receiving stuff from the client.
     *
     * @return array
     * @access public
     */
    public function getEncryptionAlgorithmsClient2Server();

    /**
     * Return a list of the (symmetric key) encryption algorithms the server
     * supports, when sending stuff to the client.
     *
     * @return array
     * @access public
     */
    public function getEncryptionAlgorithmsServer2Client();

    /**
     * Return a list of the MAC algorithms the server supports, when receiving
     * stuff from the client.
     *
     * @return array
     * @access public
     */
    public function getMACAlgorithmsClient2Server();

    /**
     * Return a list of the MAC algorithms the server supports, when sending
     * stuff to the client.
     *
     * @return array
     * @access public
     */
    public function getMACAlgorithmsServer2Client();

    /**
     * Return a list of the compression algorithms the server supports, when
     * receiving stuff from the client.
     *
     * @return array
     * @access public
     */
    public function getCompressionAlgorithmsClient2Server();

    /**
     * Return a list of the compression algorithms the server supports, when
     * sending stuff to the client.
     *
     * @return array
     * @access public
     */
    public function getCompressionAlgorithmsServer2Client();

    /**
     * Return a list of the languages the server supports, when sending stuff to
     * the client.
     *
     * @return array
     * @access public
     */
    public function getLanguagesServer2Client();

    /**
     * Return a list of the languages the server supports, when receiving stuff
     * from the client.
     *
     * @return array
     * @access public
     */
    public function getLanguagesClient2Server();

    /**
     * Returns the banner message.
     *
     * Quoting from the RFC, "in some jurisdictions, sending a warning message
     * before authentication may be relevant for getting legal protection."
     *
     * @return string
     * @access public
     */
    public function getBannerMessage();

    /**
     * Returns the server public host key.
     *
     * Caching this the first time you connect to a server and checking the
     * result on subsequent connections is recommended.  Returns false if the
     * server signature is not signed correctly with the public host key.
     *
     * @return mixed
     * @access public
     */
    public function getServerPublicHostKey();

    /**
     * Returns the exit status of an SSH command or false.
     *
     * @return false|int
     * @access public
     */
    public function getExitStatus();

    /**
     * Returns the number of columns for the terminal window size.
     *
     * @return int
     * @access public
     */
    public function getWindowColumns();
    /**
     * Returns the number of rows for the terminal window size.
     *
     * @return int
     * @access public
     */
    public function getWindowRows();

    /**
     * Sets the number of columns for the terminal window size.
     *
     * @param int $value
     * @access public
     */
    public function setWindowColumns($value);

    /**
     * Sets the number of rows for the terminal window size.
     *
     * @param int $value
     * @access public
     */
    public function setWindowRows($value);

    /**
     * Sets the number of columns and rows for the terminal window size.
     *
     * @param int $columns
     * @param int $rows
     * @access public
     */
    public function setWindowSize($columns = 80, $rows = 24);
}
