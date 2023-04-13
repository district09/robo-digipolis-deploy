<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Factory;

use District09\BackupManager\Compressors\CompressorProvider;
use District09\BackupManager\Compressors\GzipCompressor;
use District09\BackupManager\Compressors\NullCompressor;
use DigipolisGent\Robo\Task\Deploy\BackupManager\Compressors\TarCompressor;

class CompressorProviderFactory implements CompressorProviderFactoryInterface
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
