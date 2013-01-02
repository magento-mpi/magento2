<?php
/**
 * Test for Magento_Filesystem_Adapter_Local
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Filesystem_Adapter_LocalTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Filesystem_Adapter_Local
     */
    protected $_adapter;

    /**
     * @var string
     */
    protected $_filePath;

    /**
     * @var array
     */
    protected $_tearDownFiles = array();

    protected $_tearDownDirs = array();

    protected function setUp()
    {
        $this->_adapter = new Magento_Filesystem_Adapter_Local();
    }

    protected function tearDown()
    {
        foreach ($this->_tearDownDirs as $dirName) {
            if (file_exists($dirName)) {
                rmdir($dirName);
            }
        }
        foreach ($this->_tearDownFiles as $fileName) {
            if (file_exists($fileName)) {
                unlink($fileName);
            }
        }
    }

    protected function _getFilePath()
    {
        return __DIR__ . DS . '..' . DS . '_files' . DS;
    }

    /**
     * @param string $key
     * @param bool $expected
     * @dataProvider existsDataProvider
     */
    public function testExists($key, $expected)
    {
        $this->assertEquals($expected, $this->_adapter->exists($key));
    }

    /**
     * @return array
     */
    public function existsDataProvider()
    {
        return array(
            'existed file' => array($this->_getFilePath() . 'popup.csv', true),
            'not existed file' => array($this->_getFilePath() . 'popup2.css', false),
        );
    }

    /**
     * @param string $fileName
     * @param string $expectedContent
     * @dataProvider readDataProvider
     */
    public function testRead($fileName, $expectedContent)
    {
        $this->assertEquals($expectedContent, $this->_adapter->read($fileName));
    }

    /**
     * @return array
     */
    public function readDataProvider()
    {
        return array(
            'read' => array($this->_getFilePath() . 'popup.csv', 'var myData = 5;'),
        );
    }

    /**
     * @param string $fileName
     * @param string $fileData
     * @dataProvider writeDataProvider
     */
    public function testWrite($fileName, $fileData)
    {
        $this->_tearDownFiles = array($fileName);
        $this->_adapter->write($fileName, $fileData);
        $this->assertFileExists($fileName);
        $this->assertEquals(file_get_contents($fileName), $fileData);
    }

    /**
     * @return array
     */
    public function writeDataProvider()
    {
        return array(
            'correct file' => array($this->_getFilePath() . 'tempFile.css', 'temporary data'),
            'empty file' => array($this->_getFilePath() . 'tempFile2.css', '')
        );
    }

    public function testDelete()
    {
        $fileName = $this->_getFilePath() . 'tempFile3.css';
        $this->_tearDownFiles = array($fileName);
        file_put_contents($fileName, 'test data');
        $this->_adapter->delete($fileName);
        $this->assertFileNotExists($fileName);
    }

    /**
     * @param string $sourceName
     * @param string $targetName
     * @throws Exception
     * @dataProvider renameDataProvider
     */
    public function testRename($sourceName, $targetName)
    {
        $this->_tearDownFiles = array($sourceName, $targetName);
        file_put_contents($sourceName, 'test data');
        $this->_adapter->rename($sourceName, $targetName);
        $this->assertFileExists($targetName);
        $this->assertFileNotExists($sourceName);
        $this->assertEquals(file_get_contents($targetName), 'test data');
    }

    /**
     * @return array
     */
    public function renameDataProvider()
    {
        return array(
            'test 1' => array($this->_getFilePath() . 'file1.js', $this->_getFilePath() . 'file2.js'),
        );
    }

    public function testIsDirectory()
    {
        $this->assertTrue($this->_adapter->isDirectory($this->_getFilePath()));
        $this->assertFalse($this->_adapter->isDirectory($this->_getFilePath() . 'popup.csv'));
    }

    public function testCreateDirectory()
    {
        $directoryName = $this->_getFilePath() . 'new_directory';
        $this->_tearDownDirs = array($directoryName);
        $this->_adapter->createDirectory($directoryName, 0111);
        $this->assertFileExists($directoryName);
        $this->assertTrue(is_dir($directoryName));
    }

    /**
     *
     * @expectedException Magento_Filesystem_Exception
     */
    public function testCreateDirectoryError()
    {
        $this->_adapter->createDirectory('', 0777);
    }

    /**
     * @dataProvider touchDataProvider
     * @param string $fileName
     * @param bool $newFile
     */
    public function testTouch($fileName, $newFile = false)
    {
        if ($newFile) {
            $this->_tearDownFiles = array($fileName);
        }
        if ($newFile) {
            $this->assertFileNotExists($fileName);
        } else {
            $this->assertFileExists($fileName);
        }
        $this->_adapter->touch($fileName);
        $this->assertFileExists($fileName);
    }

    /**
     * @return array
     */
    public function touchDataProvider()
    {
        return array(
            'update file' => array($this->_getFilePath() . 'popup.csv', false),
            'create file' => array($this->_getFilePath() . 'popup2.css', true)
        );
    }

    /**
     * @param string $sourceName
     * @param string $targetName
     * @dataProvider renameDataProvider
     */
    public function testCopy($sourceName, $targetName)
    {
        $this->_tearDownFiles = array($sourceName, $targetName);
        $testData = 'test data';
        file_put_contents($sourceName, $testData);
        $this->_adapter->copy($sourceName, $targetName);
        $this->assertFileExists($targetName);
        $this->assertFileExists($sourceName);
        $this->assertEquals($testData, file_get_contents($targetName));
        $this->assertEquals($testData, file_get_contents($targetName));
    }
}
