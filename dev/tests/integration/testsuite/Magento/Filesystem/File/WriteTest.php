<?php
/**
 * Test for \Magento\Filesystem\Stream\Local
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\File;

use Magento\TestFramework\Helper\Bootstrap;

class WriteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Current file path
     *
     * @var string
     */
    private $currentFilePath;

    /**
     * Test instance of Write.
     */
    public function testInstance()
    {
        $file = $this->getFileInstance('popup.csv', 'r');
        $this->assertTrue($file instanceof ReadInterface);
        $this->assertTrue($file instanceof WriteInterface);
        $file->close();
    }

    /**
     * Test exceptions on attempt to open existing file with x mode
     *
     * @dataProvider fileExistProvider
     * @param $path
     * @param $mode
     * @expectedException \Magento\Filesystem\FilesystemException
     */
    public function testFileExistException($path, $mode)
    {
        $this->getFileInstance($path, $mode);
    }

    /**
     * Data provider for modeProvider
     *
     * @return array
     */
    public function fileExistProvider()
    {
        return array(
            array('popup.csv', 'x'),
            array('popup.csv', 'x+')
        );
    }

    /**
     * Test for write method
     *
     * @dataProvider writeProvider
     * @param string $path
     * @param string $mode
     * @param string $write
     * @param string $expectedResult
     */
    public function testWriteOnly($path, $mode, $write, $expectedResult)
    {
        $file = $this->getFileInstance($path, $mode);
        $result = $file->write($write);
        $file->close();
        $this->removeCurrentFile();
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Data provider for modeProvider
     *
     * @return array
     */
    public function writeProvider()
    {
        return array(
            array('new1.csv', 'w', 'write check', 11),
            array('new3.csv', 'a', 'write check', 11),
            array('new5.csv', 'x', 'write check', 11),
            array('new7.csv', 'c', 'write check', 11)
        );
    }

    /**
     * Test for write method
     *
     * @dataProvider writeAndReadProvider
     * @param string $path
     * @param string $mode
     * @param string $write
     * @param string $expectedResult
     */
    public function testWriteAndRead($path, $mode, $write, $expectedResult)
    {
        $file = $this->getFileInstance($path, $mode);
        $result = $file->write($write);
        $file->seek(0);
        $read = $file->read($result);
        $file->close();
        $this->removeCurrentFile();
        $this->assertEquals($expectedResult, $result);
        $this->assertEquals($write, $read);
    }

    /**
     * Data provider for modeProvider
     *
     * @return array
     */
    public function writeAndReadProvider()
    {
        return array(
            array('new2.csv', 'w+', 'write check', 11),
            array('new4.csv', 'a+', 'write check', 11),
            array('new6.csv', 'x+', 'write check', 11),
            array('new8.csv', 'c+', 'write check', 11)
        );
    }

    /**
     * Writes one CSV row to the file.
     *
     * @dataProvider csvProvider
     * @param string $path
     * @param array $data
     * @param string $delimiter
     * @param string $enclosure
     */
    public function testWriteCsv($path, array $data, $delimiter = ',', $enclosure = '"')
    {
        $file = $this->getFileInstance($path, 'w+');
        $result = $file->writeCsv($data, $delimiter, $enclosure);
        $file->seek(0);
        $read = $file->readCsv($result, $delimiter, $enclosure);
        $file->close();
        $this->removeCurrentFile();
        $this->assertEquals($data, $read);
    }

    /**
     * Data provider for testWriteCsv
     *
     * @return array
     */
    public function csvProvider()
    {
        return array(
            array(
                'newcsv1.csv', array('field1', 'field2'), ',', '"'
            ),
            array(
                'newcsv1.csv', array('field1', 'field2'), '%', '@'
            )
        );
    }

    /**
     * Test for lock and unlock functions
     */
    public function testLockUnlock()
    {
        $file = $this->getFileInstance('locked.csv', 'w+');
        $this->assertTrue($file->lock());
        $this->assertTrue($file->unlock());
        $file->close();
        $this->removeCurrentFile();
    }

    /**
     * Test for flush method
     */
    public function testFlush()
    {
        $file = $this->getFileInstance('locked.csv', 'w+');
        $this->assertTrue($file->flush());
        $file->close();
        $this->removeCurrentFile();
    }

    /**
     * Remove current file
     */
    private function removeCurrentFile()
    {
        unlink($this->currentFilePath);
    }

    /**
     * Get readable file instance
     * Get full path for files located in _files directory
     *
     * @param string $path
     * @param string $mode
     * @return Write
     */
    private function getFileInstance($path, $mode)
    {
        $this->currentFilePath = __DIR__ . DS . '..' . DS . '_files' . DS . $path;
        return Bootstrap::getObjectManager()
            ->create('Magento\Filesystem\File\Write', array('path' => $this->currentFilePath, 'mode' => $mode));
    }
}
