<?php

namespace DigipolisGent\Robo\Task\Deploy\Ssh;

class Command
{
    protected $command;
    protected $directory;
    protected $physicalDirectory;

    public function __construct($command, $directory = null, $physicalDirectory = false)
    {
        $this->command = $command;
        $this->directory = $directory;
        $this->physicalDirectory = $physicalDirectory;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function getDirectory()
    {
        return $this->directory;
    }

    public function getPhysicalDirectory()
    {
        return $this->physicalDirectory;
    }

    public function setDirectory($directory)
    {
        $this->directory = $directory;
        return $this;
    }

    public function setPhysicalDirectory($physicalDirectory)
    {
        $this->physicalDirectory = $physicalDirectory;
        return $this;
    }

    public function __toString()
    {
        $cd = '';
        if ($this->directory) {
            $opt = $this->physicalDirectory ? '-P ': '';
            $cd = 'cd ' . $opt . $this->directory . ' && ';
        }
        return $cd . $this->command;
    }
}
