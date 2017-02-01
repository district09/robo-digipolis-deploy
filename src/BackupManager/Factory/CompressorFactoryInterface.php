<?php

namespace DigipolisGent\Robo\Task\Deploy\BackupManager\Factory;

interface CompressorFactoryInterface
{
    /**
     * Creates a CompressorProvider.
     *
     * @return \BackupManager\Compressors\CompressorProvider
     */
    public static function create();
}
