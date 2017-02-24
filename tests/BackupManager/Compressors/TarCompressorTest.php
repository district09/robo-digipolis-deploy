<?php

namespace DigipolisGent\Tests\Robo\Task\Deploy\BackupManager\Compressors;

use DigipolisGent\Robo\Task\Deploy\BackupManager\Compressors\TarCompressor;

class TarCompressorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests TarCompressor.
     */
    public function testRun()
    {
        $unique = md5(uniqid());
        $compressor = new TarCompressor();
        $this->assertTrue($compressor->handles('tar'));
        $this->assertFalse($compressor->handles('zip'));
        $this->assertFalse($compressor->handles($unique));
        $this->assertEquals(
            "tar -zcf " . escapeshellarg($unique . '.tar.gz') . " --directory='.' " . escapeshellarg($unique) . " && rm -rf " . escapeshellarg($unique),
            $compressor->getCompressCommandLine($unique)
        );
        $this->assertEquals(
            "tar -zxf " . escapeshellarg($unique . '.tar.gz') . " --directory='.' --transform=" . escapeshellarg('s,.*,' . $unique . ',') . " && rm -rf " . escapeshellarg($unique . '.tar.gz'),
            $compressor->getDecompressCommandLine($unique . '.tar.gz')
        );
        $this->assertEquals(
            $unique . '.tar.gz',
          $compressor->getCompressedPath($unique)
        );
        $this->assertEquals(
            $unique,
            $compressor->getDecompressedPath($unique . '.tar.gz')
        );
    }

}
