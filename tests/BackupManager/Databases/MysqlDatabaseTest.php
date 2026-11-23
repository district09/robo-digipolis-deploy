<?php

namespace DigipolisGent\Tests\Robo\Task\Deploy\BackupManager\Databases;

use BackupManager\Databases\Database;
use DigipolisGent\Robo\Task\Deploy\BackupManager\Databases\MysqlDatabase;
use PHPUnit\Framework\TestCase;

class MysqlDatabaseTest extends TestCase
{
    /**
     * Tests MysqlDatabase normal run.
     */
    public function testRun()
    {
        $unique = md5(uniqid());
        $database = new MysqlDatabase();
        $database->setTableList([
            'users',
            'cache',
            'watchdog',
        ]);
        $database->setConfig(
            [
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
            ]
        );

        $this->assertTrue($database->handles('mysql'));
        $this->assertFalse($database->handles('mongodb'));
        $this->assertFalse($database->handles($unique));
        $this->assertEquals(
            "(mysqldump --host='localhost' --port='3306' --user='root' "
            . "--password='password' 'test'  --routines --single-transaction "
            . "--opt ) > '{$unique}.sql'",
            $database->getDumpCommandLine($unique . '.sql')
        );
    }

    /**
     * Tests MysqlDatabase with tables option.
     */
    public function testRunTables()
    {
        $unique = md5(uniqid());
        $database = new MysqlDatabase();
        $database->setTableList([
            'users',
            'cache',
            'watchdog',
        ]);
        $database->setConfig(
            [
                'type' => 'mysql',
                'host' => 'localhost',
                'port' => '3306',
                'user' => 'root',
                'pass' => 'password',
                'database' => 'test',
                'ignoreTables' => [],
                'structureTables' => [],
                'tables' => ['users'],
                'dataOnly' => false,
                'orderedDump' => false,
                'singleTransaction' => true,
                'extra' => '--opt',
            ]
        );

        $this->assertTrue($database->handles('mysql'));
        $this->assertFalse($database->handles('mongodb'));
        $this->assertFalse($database->handles($unique));
        $this->assertEquals(
            "(mysqldump --host='localhost' --port='3306' --user='root' "
            . "--password='password' 'test' 'users' --routines --single-transaction "
            . "--opt ) > '{$unique}.sql'",
            $database->getDumpCommandLine($unique . '.sql')
        );
    }

    /**
     * Tests MysqlDatabase with structureTables option.
     */
    public function testRunStructureTables()
    {
        $unique = md5(uniqid());
        $database = new MysqlDatabase();
        $database->setTableList([
            'users',
            'cache',
            'watchdog',
        ]);
        $database->setConfig(
            [
                'type' => 'mysql',
                'host' => 'localhost',
                'port' => '3306',
                'user' => 'root',
                'pass' => 'password',
                'database' => 'test',
                'ignoreTables' => [],
                'structureTables' => ['cache', 'watchdog'],
                'tables' => [],
                'dataOnly' => false,
                'orderedDump' => false,
                'singleTransaction' => true,
                'extra' => '--opt',
            ]
        );

        $this->assertTrue($database->handles('mysql'));
        $this->assertFalse($database->handles('mongodb'));
        $this->assertFalse($database->handles($unique));
        $this->assertEquals(
            "(mysqldump --host='localhost' --port='3306' --user='root' "
            . "--password='password' 'test'  --routines --single-transaction "
            . "--opt --ignore-table='test.cache' --ignore-table='test.watchdog' "
            . "&& mysqldump --host='localhost' --port='3306' --user='root' "
            . "--password='password' 'test' 'cache' 'watchdog' --no-data "
            . "--routines --single-transaction --opt) > '{$unique}.sql'",
            $database->getDumpCommandLine($unique . '.sql')
        );
    }

    /**
     * Tests MysqlDatabase with ignoreTables option.
     */
    public function testRunIgnoreTables()
    {
        $unique = md5(uniqid());
        $database = new MysqlDatabase();
        $database->setTableList([
            'users',
            'cache',
            'watchdog',
        ]);
        $database->setConfig(
            [
                'type' => 'mysql',
                'host' => 'localhost',
                'port' => '3306',
                'user' => 'root',
                'pass' => 'password',
                'database' => 'test',
                'ignoreTables' => ['cache', 'watchdog'],
                'structureTables' => [],
                'tables' => [],
                'dataOnly' => false,
                'orderedDump' => false,
                'singleTransaction' => true,
                'extra' => '--opt',
            ]
        );

        $this->assertTrue($database->handles('mysql'));
        $this->assertFalse($database->handles('mongodb'));
        $this->assertFalse($database->handles($unique));
        $this->assertEquals(
            "(mysqldump --host='localhost' --port='3306' --user='root' "
            . "--password='password' 'test'  --routines --single-transaction "
            . "--opt --ignore-table='test.cache' --ignore-table='test.watchdog') "
            . "> '{$unique}.sql'",
            $database->getDumpCommandLine($unique . '.sql')
        );
    }

    /**
     * Tests MysqlDatabase with ignoreTables and structureTables option.
     */
    public function testRunIgnoreAndStructureTables()
    {
        $unique = md5(uniqid());
        $database = new MysqlDatabase();
        $database->setTableList([
            'users',
            'cache',
            'watchdog',
        ]);
        $database->setConfig(
            [
                'type' => 'mysql',
                'host' => 'localhost',
                'port' => '3306',
                'user' => 'root',
                'pass' => 'password',
                'database' => 'test',
                'ignoreTables' => ['cache', 'watchdog'],
                'structureTables' => ['users'],
                'tables' => [],
                'dataOnly' => false,
                'orderedDump' => false,
                'singleTransaction' => true,
                'extra' => '--opt',
            ]
        );

        $this->assertTrue($database->handles('mysql'));
        $this->assertFalse($database->handles('mongodb'));
        $this->assertFalse($database->handles($unique));
        $this->assertEquals(
            "(mysqldump --host='localhost' --port='3306' --user='root' "
            . "--password='password' 'test'  --routines --single-transaction "
            . "--opt --ignore-table='test.users' --ignore-table='test.cache' "
            . "--ignore-table='test.watchdog' && mysqldump --host='localhost' "
            . "--port='3306' --user='root' --password='password' 'test' 'users' "
            . "--no-data --routines --single-transaction --opt) "
            . "> '{$unique}.sql'",
            $database->getDumpCommandLine($unique . '.sql')
        );
    }

    /**
     * Tests MysqlDatabase with tables and structureTables option.
     */
    public function testRunTablesAndStructureTables()
    {
        $unique = md5(uniqid());
        $database = new MysqlDatabase();
        $database->setTableList([
            'users',
            'cache',
            'watchdog',
        ]);
        $database->setConfig(
            [
                'type' => 'mysql',
                'host' => 'localhost',
                'port' => '3306',
                'user' => 'root',
                'pass' => 'password',
                'database' => 'test',
                'ignoreTables' => [],
                'structureTables' => ['cache', 'watchdog'],
                'tables' => ['users', 'cache', 'watchdog'],
                'dataOnly' => false,
                'orderedDump' => false,
                'singleTransaction' => true,
                'extra' => '--opt',
            ]
        );

        $this->assertTrue($database->handles('mysql'));
        $this->assertFalse($database->handles('mongodb'));
        $this->assertFalse($database->handles($unique));
        $this->assertEquals(
            "(mysqldump --host='localhost' --port='3306' --user='root' "
            . "--password='password' 'test' 'users' --routines "
            . "--single-transaction --opt  && mysqldump --host='localhost' "
            . "--port='3306' --user='root' --password='password' 'test' "
            . "'cache' 'watchdog' --no-data --routines --single-transaction "
            . "--opt) > '{$unique}.sql'",
            $database->getDumpCommandLine($unique . '.sql')
        );
    }

    /**
     * Tests that restores read the dump from stdin with an escaped path.
     */
    public function testRestore()
    {
        $database = new MysqlDatabase();
        $database->setConfig(
            [
                'type' => 'mysql',
                'host' => 'localhost',
                'port' => '3306',
                'user' => 'root',
                'pass' => 'password',
                'database' => 'test',
                'structureTables' => ['cache'],
                'extra' => '--opt',
            ]
        );

        $this->assertEquals(
            "mysql --host='localhost' --port='3306' --user='root' "
            . "--password='password'  'test' < '/tmp/dump.sql'",
            $database->getRestoreCommandLine('/tmp/dump.sql')
        );
        // A path with shell and mysql client syntax stays a single argument.
        $this->assertStringEndsWith(
            " < '/tmp/\";\nsystem rm -rf ~; echo \"'",
            $database->getRestoreCommandLine("/tmp/\";\nsystem rm -rf ~; echo \"")
        );
    }

    /**
     * Tests that restores are delegated to the decorated database.
     */
    public function testRestoreIsDelegated()
    {
        $config = ['database' => 'test'];
        $inner = $this->createMock(Database::class);
        $inner->expects($this->once())->method('setConfig')->with($config);
        $inner->expects($this->once())
            ->method('getRestoreCommandLine')
            ->with('dump.sql')
            ->willReturn('restore-command');

        $database = new MysqlDatabase($inner);
        $database->setConfig($config);
        $this->assertEquals('restore-command', $database->getRestoreCommandLine('dump.sql'));
    }
}
