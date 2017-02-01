<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Databases;

use BackupManager\Databases\MysqlDatabase as Mysql;

class MysqlDatabase extends Mysql
{

    /**
     * Config for this database.
     *
     * @var array
     */
    protected $config;

    /**
     * {@inheritdoc}
     */
    public function handles($type)
    {
        return strtolower($type) == 'mysql';
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getDumpCommandLine($outputPath)
    {
        $this->config += [
            'ignoreTables' => [],
            'structureTables' => [],
            'tables' => [],
            'dataOnly' => false,
            'orderedDump' => false,
            'singleTransaction' => true,
            'extra' => '',
        ];

        $command = '(mysqldump {{credentials}} {{tables}} {{options}} {{ignore}}';
        $credentials = $this->getCredentials();
        $tables = $this->getTables();
        $options = $this->getOptions();
        $ignore = $this->getIgnore();
        $structureTables = $this->getStructureTables();
        $file = escapeshellarg($outputPath);
        if ($structureTables) {
            $command .= ' && mysqldump {{credentials}} {{structureTables}} --no-data {{options}}';
        }
        $command .= ') > {{file}}';
        return str_replace(
            [
                '{{credentials}}',
                '{{tables}}',
                '{{options}}',
                '{{ignore}}',
                '{{structureTables}}',
                '{{file}}',
            ],
            [
                $credentials,
                $tables,
                $options,
                $ignore,
                $structureTables,
                $file,
            ],
            $command
        );
    }

    /**
     * Get the credential options for the command.
     *
     * @return string
     */
    protected function getCredentials()
    {
        // Database credentials.
        $credentialArgs = [
            '--host=%s --port=%s --user=%s --password=%s %s',
            escapeshellarg($this->config['host']),
            escapeshellarg($this->config['port']),
            escapeshellarg($this->config['user']),
            escapeshellarg($this->config['pass']),
            escapeshellarg($this->config['database']),
        ];
        return call_user_func_array('sprintf', $credentialArgs);
    }

    /**
     * Get the table arguments for the command.
     *
     * @return string
     */
    protected function getTables()
    {
        if (empty($this->config['tables'])) {
            return '';
        }
        $tables = [];
        foreach ($this->config['tables'] as $table) {
            $tables[] = escapeshellarg($table);
        }
        return implode($tables);
    }

    /**
     * Get the command options for the command.
     *
     * @return string
     */
    protected function getOptions()
    {
        $options = [
            '--routines',
        ];

        if ($this->config['dataOnly']) {
            $options[] = '--no-create-info';
        }
        if ($this->config['orderedDump']) {
            $options[] = '--skip-extended-insert';
            $options[] = '--order-by-primary';
        }
        if ($this->config['singleTransaction']) {
            $options[] = '--single-transaction';
        }
        if ($this->config['extra']) {
            $options[] = $this->config['extra'];
        }
        return implode(' ', $options);
    }

    /**
     * Get the ignore table options for the command.
     *
     * @return string
     */
    protected function getIgnore()
    {
        if (!empty($this->config['tables'])) {
            return '';
        }
        $ignoreTables = array_merge($this->config['structureTables'], $this->config['ignoreTables']);
        // Append the ignore-table options.
        $ignoreArgs = [];
        foreach ($ignoreTables as $table) {
            $ignoreArgs[] = escapeshellarg($this->config['database'] . '.' . $table);
        }
        array_unshift($ignoreArgs, str_repeat(' --ignore-table=%s', count($ignoreArgs)));
        return trim(call_user_func_array('sprintf', $ignoreArgs));
    }

    /**
     * Get the structure tables for the command.
     *
     * @return string
     */
    protected function getStructureTables()
    {
        if (!empty($this->config['tables']) || empty($this->config['structureTables'])) {
            return '';
        }
        $structureTables = [];
        foreach ($this->config['structureTables'] as $table) {
            $structureTables[] = escapeshellarg($table);
        }
        return implode(' ', $structureTables);
    }
}
