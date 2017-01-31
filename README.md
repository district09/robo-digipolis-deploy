# Robo Digipolis Deploy

Deploy tasks for Robo Task Runner

## Tasks in this package

### PushPackage
```php
$auth = new \DigipolisGent\Robo\Task\Deploy\Ssh\Auth\KeyFile('user', '/home/myuser/.ssh/id_dsa');
$result = $this->taskPushPackage('192.168.1.1', $auth)
    ->port(8022)
    ->timeout(15)
    ->destinationFolder('/folder/on/server')
    ->package('/path/to/local/package.tar.gz')
    ->run();
```

### Scp
```php
$auth = new \DigipolisGent\Robo\Task\Deploy\Ssh\Auth\KeyFile('user', '/home/myuser/.ssh/id_dsa');
$result = $this->taskScp('192.168.1.1', $auth)
    ->port(8022)
    ->timeout(15)
    // Download file from server.
    ->get('/path/to/remote/file.txt', '/path/to/local/file.txt')
    // Upload file to server.
    ->put('/path/to/remote/file.txt', '/path/to/local/file.txt')
    ->run();
```

### Ssh
```php
$auth = new \DigipolisGent\Robo\Task\Deploy\Ssh\Auth\KeyFile('user', '/home/myuser/.ssh/id_dsa');
$result = $this->taskSsh('192.168.1.1', $auth)
    ->port(8022)
    ->timeout(15)
    // Set the remote directory to execute the commands in.
    ->remoteDirectory('/path/to/remote/dir')
    ->exec('composer install')
    ->run();
```

### SymlinkFolderFileContents
```php
$result = $this
    ->taskSymlinkFolderFileContents('/path/to/source', '/path/to/destination')
    ->run();
```

As this command will most likely be used to symlink config files on a server during deployment,
this task should be used in a command that runs on the server. For example:

RoboFile.php on the server (let's say 192.168.1.1 in folder /path/to/remote/dir):
```php
<?php

class RoboFile extends \Robo\Tasks
{
    use \DigipolisGent\Robo\Task\Deploy\loadTasks;

    /**
     * Creates the symlinks.
     */
    public function symlinks($source, $dest)
    {
        $this
            ->taskSymlinkFolderFileContents($source, $dest)
            ->run();
    }
}

```

RoboFile.php on the build server / your local machine:
```php
<?php

class RoboFile extends \Robo\Tasks
{
    use \DigipolisGent\Robo\Task\Deploy\loadTasks;

    /**
     * Creates the symlinks.
     */
    public function symlinks($source, $dest)
    {
        $auth = new \DigipolisGent\Robo\Task\Deploy\Ssh\Auth\KeyFile('user', '/home/myuser/.ssh/id_dsa');
        $this->taskSsh('192.168.1.1', $auth)
            ->port(8022)
            ->timeout(15)
            ->remoteDirectory('/path/to/remote/dir')
            ->exec('vendor/bin/robo symlink ' . $source . ' ' . $dest)
            ->run();
    }
}

```
