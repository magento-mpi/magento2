<?php
/**
 * Test for \Magento\Filesystem\File\Read
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\File;

use Magento\TestFramework\Helper\Bootstrap;

class ReadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test instance of Read
     */
    public function testInstance()
    {
        $file = $this->getFileInstance('popup.csv');
        $this->assertTrue($file instanceof ReadInterface);
    }

    /**
     * Test for assertValid method
     * Expected exception for file that does not exist and file without access
     *
     * @dataProvider providerNotValidFiles
     * @param string $path
     * @expectedException \Magento\Filesystem\FilesystemException
     */
    public function testAssertValid($path)
    {
        $this->getFileInstance($path);
    }

    /**
     * Data provider for testAssertValid
     *
     * @return array
     */
    public function providerNotValidFiles()
    {
        return array(
            array('invalid.csv'), //File does not exist
        );
    }

    /**
     * Test for read method
     *
     * @dataProvider providerRead
     * @param string $path
     * @param int $length
     * @param string $expectedResult
     */
    public function testRead($path, $length, $expectedResult)
    {
        $file = $this->getFileInstance($path);
        $result = $file->read($length);
        $this->assertEquals($result, $expectedResult);
    }

    /**
     * Data provider for testRead
     *
     * @return array
     */
    public function providerRead()
    {
        return array(
            array('popup.csv', 10, 'var myData'),
            array('popup.csv', 15, 'var myData = 5;')
        );
    }

    /**
     * Test for readCsv method
     *
     * @dataProvider providerCsv
     * @param string $path
     * @param int $length
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @param array $expectedRow1
     * @param array $expectedRow2
     */
    public function testReadCsv($path, $length, $delimiter, $enclosure, $escape, $expectedRow1, $expectedRow2)
    {
        $file = $this->getFileInstance($path);
        $actualRow1 = $file->readCsv($length, $delimiter, $enclosure, $escape);
        $actualRow2 = $file->readCsv($length, $delimiter, $enclosure, $escape);
        $this->assertEquals($expectedRow1, $actualRow1);
        $this->assertEquals($expectedRow2, $actualRow2);
    }

    /**
     * Data provider for testReadCsv
     *
     * @return array
     */
    public function providerCsv()
    {
        return array(
            array('data.csv', 0, ',', '"', '\\', array('field1', 'field2'), array('field3', 'field4'))
        );
    }

    /**
     * Test for tell method
     *
     * @dataProvider providerPosition
     * @param string $path
     * @param int $position
     */
    public function testTell($path, $position)
    {
        $file = $this->getFileInstance($path);
        $file->read($position);
        $this->assertEquals($position, $file->tell());
    }

    /**
     * Data provider for testTell
     *
     * @return array
     */
    public function providerPosition()
    {
        return array(
            array('popup.csv', 5),
            array('popup.csv', 10)
        );
    }

    /**
     * Test for seek method
     *
     * @dataProvider providerSeek
     * @param string $path
     * @param int $position
     * @param int $whence
     * @param int $tell
     */
    public function testSeek($path, $position, $whence, $tell)
    {
        $file = $this->getFileInstance($path);
        $file->seek($position, $whence);
        $this->assertEquals($tell, $file->tell());
    }

    /**
     * Data provider for testSeek
     *
     * @return array
     */
    public function providerSeek()
    {
        return array(
            array('popup.csv', 5, SEEK_SET, 5),
            array('popup.csv', 10, SEEK_CUR, 10),
            array('popup.csv', -10, SEEK_END, 5)
        );
    }

    /**
     * Test for eof method
     *
     * @dataProvider providerEof
     * @param string $path
     * @param int $position
     */
    public function testEofFalse($path, $position)
    {
        $file = $this->getFileInstance($path);
        $file->seek($position);
        $this->assertFalse($file->eof());
    }

    /**
     * Data provider for testEofTrue
     *
     * @return array
     */
    public function providerEof()
    {
        return array(
            array('popup.csv', 5, false),
            array('popup.csv', 10, false),
        );
    }

    /**
     * Test for eof method
     */
    public function testEofTrue()
    {
        $file = $this->getFileInstance('popup.csv');
        $file->seek(0, SEEK_END);
        $file->read(1);
        $this->assertTrue($file->eof());
    }

    /**
     * Test for close method
     */
    public function testClose()
    {
        $file = $this->getFileInstance('popup.csv');
        $this->assertTrue($file->close());
    }

    /**
     * Get readable file instance
     * Get full path for files located in _files directory
     *
     * @param $path
     * @return Read
     */
    private function getFileInstance($path)
    {
        $fullPath = __DIR__ . '/../_files/' . $path;
        return Bootstrap::getObjectManager()
            ->create(
                'Magento\Filesystem\File\Read',
                array(
                    'path' => $fullPath,
                    'driver' => new \Magento\Filesystem\Driver\File()
                )
            );
    }
}
