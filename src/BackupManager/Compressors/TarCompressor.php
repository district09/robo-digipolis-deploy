<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Compressors;

use BackupManager\Compressors\Compressor;

class TarCompressor extends Compressor
{

    /**
     * {@inheritdoc}
     */
    public function handles($type)
    {
        return strtolower($type) == 'tar';
    }

    /**
     * {@inheritdoc}
     */
    public function getCompressCommandLine($inputPath)
    {
        return 'tar -zcf '
            . escapeshellarg($this->getCompressedPath($inputPath))
            . ' --directory=' . escapeshellarg(dirname($inputPath))
            . ' ' . escapeshellarg(basename($inputPath))
            . ' && rm -rf ' . escapeshellarg($inputPath);
    }

    /**
     * {@inheritdoc}
     */
    public function getDecompressCommandLine($outputPath)
    {
        return 'tar -zxf '
            . escapeshellarg($outputPath)
            . ' --directory=' . escapeshellarg(dirname($outputPath))
            . ' --transform=' . escapeshellarg('s,.*,' . $this->getDecompressedPath(basename($outputPath)) . ',')
            . ' && rm -rf ' . escapeshellarg($outputPath);

    }

    /**
     * {@inheritdoc}
     */
    public function getCompressedPath($inputPath)
    {
        return $inputPath . '.tar.gz';
    }

    /**
     * {@inheritdoc}
     */
    public function getDecompressedPath($inputPath)
    {
        return preg_replace('/\.tar\.gz$/', '', $inputPath);
    }
}
