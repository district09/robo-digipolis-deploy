# Robo Digipolis Deploy

Deploy tasks for Robo Task Runner

[![Latest Stable Version](https://poser.pugx.org/digipolisgent/robo-digipolis-deploy/v/stable)](https://packagist.org/packages/digipolisgent/robo-digipolis-deploy)
[![Latest Unstable Version](https://poser.pugx.org/digipolisgent/robo-digipolis-deploy/v/unstable)](https://packagist.org/packages/digipolisgent/robo-digipolis-deploy)
[![Total Downloads](https://poser.pugx.org/digipolisgent/robo-digipolis-deploy/downloads)](https://packagist.org/packages/digipolisgent/robo-digipolis-deploy)
[![License](https://poser.pugx.org/digipolisgent/robo-digipolis-deploy/license)](https://packagist.org/packages/digipolisgent/robo-digipolis-deploy)

[![Build Status](https://travis-ci.org/digipolisgent/robo-digipolis-deploy.svg?branch=develop)](https://travis-ci.org/digipolisgent/robo-digipolis-deploy)
[![Maintainability](https://api.codeclimate.com/v1/badges/eced33c8f045644486cb/maintainability)](https://codeclimate.com/github/digipolisgent/robo-digipolis-deploy/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/eced33c8f045644486cb/test_coverage)](https://codeclimate.com/github/digipolisgent/robo-digipolis-deploy/test_coverage)
[![PHP 7 ready](https://php7ready.timesplinter.ch/digipolisgent/robo-digipolis-deploy/develop/badge.svg)](https://travis-ci.org/digipolisgent/robo-digipolis-deploy)

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

### SFTP

```php
$auth = new \DigipolisGent\Robo\Task\Deploy\Ssh\Auth\KeyFile('user', '/home/myuser/.ssh/id_dsa');
$result = $this->taskSFTP('192.168.1.1', $auth)
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

As this command will most likely be used to symlink config files on a server
during deployment, this task should be used in a command that runs on the
server. For example:

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

### DatabaseBackup

```php
$filesystemConfig = [
    'local' => [
        'type' => 'Local',
        'root' => '/home/myuser/backups',
    ],
];

$dbConfig = [
    'development' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'port' => '3306',
        'user' => 'root',
        'pass' => 'password',
        'database' => 'test',
        'singleTransaction' => true,
        'ignoreTables' => [],
    ],
    'production' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'port' => '3306',
        'user' => 'root',
        'pass' => 'password',
        'database' => 'test',
        'ignoreTables' => [],
        'structureTables' => [],
        'tables' => [],
        'dataOnly' => false,
        'orderedDump' => false,
        'singleTransaction' => true,
        'extra' => '--opt',
    ],
];
// Store a backup of the development database in /home/myuser/backups/dev.sql.tar.gz.
$result = $this->taskDatabaseBackup($filesystemConfig, $dbConfig)
    ->database('development')
    ->destination('dev.sql')
    ->compression('tar')
    ->run();

```

### DatabaseRestore

```php
$filesystemConfig = [
    'local' => [
        'type' => 'Local',
        'root' => '/home/myuser/backups',
    ],
];

$dbConfig = [
    'development' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'port' => '3306',
        'user' => 'root',
        'pass' => 'password',
        'database' => 'test',
        'singleTransaction' => true,
        'ignoreTables' => [],
    ],
    'production' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'port' => '3306',
        'user' => 'root',
        'pass' => 'password',
        'database' => 'test',
        'ignoreTables' => [],
        'structureTables' => [],
        'tables' => [],
        'dataOnly' => false,
        'orderedDump' => false,
        'singleTransaction' => true,
        'extra' => '--opt',
    ],
];
// Restore a backup of the development database located at /home/myuser/backups/dev.sql.tar.gz.
$result = $this->taskDatabaseRestore($filesystemConfig, $dbConfig)
    ->database('development')
    ->source('dev.sql.tar.gz')
    ->compression('tar')
    ->run();

```

#### File system configuration options

More information on the configuration options for the file systems can be found
at <https://github.com/backup-manager/backup-manager>.

#### Database configuration options

More information on the configuration options for the databases can be found
at <https://github.com/backup-manager/backup-manager>. However, we provide our
own MySql database handler. The configuration options are explained below:

```php
$dbConfig = [
    'production' => [
        // Specify it's a mysql database.
        'type' => 'mysql',
        // Specify the database credentials.
        'host' => 'localhost',
        'port' => '3306',
        'user' => 'root',
        'pass' => 'password',
        'database' => 'test',
        // Tables to exclude from the export. This option will be ignored if the
        // 'tables' configuration option is set, because all tables will be
        // excluded except the ones specified in the 'tables' option. Therefore,
        // adding a table to the 'ignoreTables' would be the same as omitting it
        // from tbe 'tables' option if that option has a non-empty) value.
        'ignoreTables' => [],
        // Tables to only export the table structure for. A good example would
        // be a cache table, since most of the time you wouldn't want this
        // table's data in a backup. The structure of the tables specified here
        // will be exported even if the 'tables' configuration options has a
        // (non-empty) value and these tables are not in it.
        'structureTables' => [],
        // Tables to export. Leave empty to export all tables (except those
        // specified in the 'ignoreTables' configuration option).
        'tables' => [],
        // Export only data, not table structure.
        'dataOnly' => false,
        // Order by primary key and add line breaks for efficient diff in
        // revision control. Slows down the dump.
        'orderedDump' => false,
        // If singleTransaction is set to true, the --single-transcation flag
        // will be set. This is useful on transactional databases like InnoDB.
        // http://dev.mysql.com/doc/refman/5.7/en/mysqldump.html#option_mysqldump_single-transaction
        'singleTransaction' => true,
        // Extra options to pass to mysqldump (e.g. '--opt --quick').
        'extra' => '--opt',

    ],
];
```

## Commands in this package

This package provides default commands wich you can use in your `RoboFile.php`
like so:

```php
class RoboFile extends \Robo\Tasks
{
    use \DigipolisGent\Robo\Task\Deploy\Commands\loadCommands;
}
```

### digipolis:database-backup

`vendor/bin/robo digipolis:database-backup [DATABASE] [OPTIONS]`

#### Arguments

##### DATABASE

The database config key. See above for more details. In de given example, this
argument would have to be 'production'. Defaults to 'default'.

#### Options

##### --file-system-config, -fsconf

Path to a PHP file containing filesystem config as documented at
<https://github.com/backup-manager/backup-manager>. Defaults to the root
directory of the local filesystem.

##### --database-config, -dbconf

Path to a PHP file containing database config as documented at
<https://github.com/backup-manager/backup-manager>. Defaults to a database with
the name of the current working directory as database name, on `localhost` port
`3306`, user `root` and an empty password.

##### --compression, -c

The compression to use for this backup. Defaults to `tar`.

##### --destination, -d

The destination file for this backup. Defaults to `project.tar.gz` in the
current working directory.

##### --destination-type, -dtype

The destination type (e.g. `local`, `dropbox`, `ftp`). Defaults to `local`.

### digipolis:database-restore

`vendor/bin/robo digipolis:database-restore [DATABASE] [OPTIONS]`

#### Arguments

##### DATABASE

The database config key. See above for more details. In de given example, this
argument would have to be 'production'. Defaults to 'default'.

#### Options

##### --file-system-config, -fsconf

Path to a PHP file containing filesystem config as documented at
<https://github.com/backup-manager/backup-manager>. Defaults to the root
directory of the local filesystem.

##### --database-config, -dbconf

Path to a PHP file containing database config as documented at
<https://github.com/backup-manager/backup-manager>. Defaults to a database with
the name of the current working directory as database name, on `localhost` port
`3306`, user `root` and an empty password.

##### --compression, -c

The compression of the given backup. Defaults to `tar`.

##### --source, -s

The source file to restore. Defaults to `project.tar.gz` in the current working
directory.

##### --source-type, -stype

The source type (e.g. `local`, `dropbox`, `ftp`). Defaults to `local`.

### digipolis:push-package

`vendor/bin/robo digipolis:push-package USER HOST PACKAGE [DESTINATION] [OPTIONS]`

#### Arguments

##### USER

The user to connect to the host.

##### HOST

The host to connect to.

##### PACKAGE

The package (tar-file) to push.

##### DESTINATION

The destination folder on the server. Defaults to the home directory of the
user.

#### Options

##### --password

The password to connect to the host.

##### --key-file

The private key file to connect to the host.

##### --port

The port to connect on. Defaults to `22`.

##### --timeout

The timeout for the connection in seconds. Defaults to `10`.
