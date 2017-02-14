<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Factory;

interface CompressorProviderFactoryInterface
{
    /**
     * Creates a CompressorProvider.
     *
     * @return \BackupManager\Compressors\CompressorProvider
     */
    public static function create();
}
