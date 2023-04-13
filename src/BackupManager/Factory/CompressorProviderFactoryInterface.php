<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Factory;

interface CompressorProviderFactoryInterface
{
    /**
     * Creates a CompressorProvider.
     *
     * @return \District09\BackupManager\Compressors\CompressorProvider
     */
    public static function create();
}
