<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Compressors;

use BackupManager\Compressors\Compressor;

class TarCompressor implements Compressor
{

    /**
     * {@inheritdoc}
     */
    public function handles(?string $type): bool
    {
        return strtolower($type ?? '') == 'tar';
    }

    /**
     * {@inheritdoc}
     */
    public function getCompressCommandLine(string $inputPath): string
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
    public function getDecompressCommandLine(string $outputPath): string
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
    public function getCompressedPath(string $inputPath): string
    {
        return $inputPath . '.tar.gz';
    }

    /**
     * {@inheritdoc}
     */
    public function getDecompressedPath(string $inputPath): string
    {
        return preg_replace('/\.tar\.gz$/', '', $inputPath);
    }
}
