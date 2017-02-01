<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Factory;

use BackupManager\Compressors\CompressorProvider;
use BackupManager\Compressors\GzipCompressor;
use BackupManager\Compressors\NullCompressor;
use DigipolisGent\Robo\Task\Deploy\BackupManager\Compressors\TarCompressor;

class CompressorFactory implements CompressorFactoryInterface
{

    /**
     * {@inheritdoc}
     */
    public static function create()
    {
        // Add all default compressors.
        $compressorProvider = new CompressorProvider();
        $compressorProvider->add(new TarCompressor());
        $compressorProvider->add(new GzipCompressor());
        $compressorProvider->add(new NullCompressor());

        return $compressorProvider;
    }
}
