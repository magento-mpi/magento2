<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework;

class ArchiveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Archive
     */
    protected $archive;

    /**
     * @var string
     */
    protected $sourceFilePath;

    /**
     * @var string
     */
    protected $destinationDir;

    /**
     * @var string
     */
    protected $packed;

    /**
     * @var string
     */
    protected $unpacked;

    protected function setUp()
    {
        $this->archive = new Archive();
        $this->sourceFilePath = __DIR__ . '/_files/Archive/source.txt';
        $this->destinationDir = __DIR__ . '/_files/Archive/archives/';
    }

    protected function tearDown()
    {
        if (!empty($this->packed) && file_exists($this->packed)) {
            unlink($this->packed);
            $this->packed = null;
        }
        if (!empty($this->unpacked) && file_exists($this->unpacked)) {
            unlink($this->unpacked);
            $this->unpacked = null;
        }
    }

    /**
     * @dataProvider isArchiveProvider
     * @param string $file
     * @param bool $isArchive
     */
    public function testIsArchive($file, $isArchive)
    {
        $this->assertEquals($isArchive, $this->archive->isArchive($file));
    }

    public function isArchiveProvider()
    {
        return [
            ['archive.tar', true],
            ['archive.gz', true],
            ['archive.gzip', true],
            ['archive.tgz', true],
            ['archive.tgzip', true],
            ['archive.bz', true],
            ['archive.bzip', true],
            ['archive.bzip2', true],
            ['archive.bz2', true],
            ['archive.tbz', true],
            ['archive.tbzip', true],
            ['archive.tbz2', true],
            ['archive.tbzip2', true],
            ['archive.txt', false],
            ['archive.php', false],
            ['archive.phtml', false],
            ['archive.js', false],
            ['archive.log', false],
        ];
    }

    /**
     * @dataProvider isTarProvider
     * @param string $file
     * @param bool $isArchive
     */
    public function testIsTar($file, $isArchive)
    {
        $this->assertEquals($isArchive, $this->archive->isTar($file));
    }

    public function isTarProvider()
    {
        return [
            ['archive.tar', true],
            ['archive.gz', false],
            ['archive.gzip', false],
            ['archive.tgz', false],
            ['archive.tgzip', false],
            ['archive.bz', false],
            ['archive.bzip', false],
            ['archive.bzip2', false],
            ['archive.bz2', false],
            ['archive.tbz', false],
            ['archive.tbzip', false],
            ['archive.tbz2', false],
            ['archive.tbzip2', false],
            ['archive.txt', false],
            ['archive.php', false],
            ['archive.phtml', false],
            ['archive.js', false],
            ['archive.log', false],
        ];
    }

    /**
     * @dataProvider destinationProvider
     * @param string $destinationFile
     */
    public function testPackUnpackGzBz($destinationFile)
    {
        $this->packed = $this->archive->pack($this->sourceFilePath, $this->destinationDir . $destinationFile);

        $this->assertFileExists($this->packed);
        $this->assertEquals($this->destinationDir . $destinationFile, $this->packed);

        $this->unpacked = $this->archive->unpack($this->packed, $this->destinationDir);

        $this->assertFileExists($this->unpacked);
        $this->assertStringStartsWith($this->destinationDir, $this->unpacked);
    }

    public function destinationProvider()
    {
        return [
            ['archive.gz', false],
            ['archive.gzip', false],
            ['archive.bz', false],
            ['archive.bzip', false],
            ['archive.bzip2', false],
            ['archive.bz2', false]
        ];
    }

    /**
     * @dataProvider tarProvider
     * @param string $destinationFile
     */
    public function testPackUnpackTar($destinationFile)
    {
        $this->packed = $this->archive->pack($this->sourceFilePath, $this->destinationDir . $destinationFile);

        $this->assertFileExists($this->packed);
        $this->assertEquals($this->destinationDir . $destinationFile, $this->packed);

        $unpacked = $this->archive->unpack($this->packed, $this->destinationDir);

        $this->unpacked = $unpacked . pathinfo($this->sourceFilePath, PATHINFO_BASENAME);

        $this->assertFileExists($this->unpacked);
        $this->assertStringStartsWith($this->destinationDir, $this->unpacked);
    }

    /**
     * @dataProvider tarProvider
     * @param string $destinationFile
     */
    public function testExtract($destinationFile)
    {
        $this->packed = $this->archive->pack($this->sourceFilePath, $this->destinationDir . $destinationFile);

        $this->assertFileExists($this->packed);
        $this->assertEquals($this->destinationDir . $destinationFile, $this->packed);

        $filename = pathinfo($this->sourceFilePath, PATHINFO_BASENAME);
        $this->unpacked = $this->archive->extract($filename, $this->packed, $this->destinationDir);

        $this->assertFileExists($this->unpacked);
        $this->assertStringStartsWith($this->destinationDir, $this->unpacked);
    }

    public function tarProvider()
    {
        return [
            ['archive.tar', true],
            ['archive.tgz', false],
            ['archive.tgzip', false],
            ['archive.tbz', false],
            ['archive.tbzip', false],
            ['archive.tbz2', false],
            ['archive.tbzip2', false]
        ];
    }
}
