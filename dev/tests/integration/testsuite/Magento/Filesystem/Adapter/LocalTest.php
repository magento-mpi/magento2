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

    protected function setUp()
    {
        $this->_adapter = new Magento_Filesystem_Adapter_Local();
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
        $filesPath = __DIR__ . DS . '..' . DS . '_files' . DS;
        return array(
            'existed file' => array($filesPath . 'popup.csv', true),
            'not existed file' => array($filesPath . 'popup2.css', false),
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
        $filesPath = __DIR__ . DS . '..' . DS . '_files' . DS;
        return array(
            'read' => array($filesPath . 'popup.csv', 'var myData = 5;'),
        );
    }

    /**
     * @param string $fileName
     * @param string $fileData
     * @dataProvider writeDataProvider
     * @throws Exception
     */
    public function testWrite($fileName, $fileData)
    {
        try {
            $this->_adapter->write($fileName, $fileData);
            $this->assertFileExists($fileName);
            $this->assertEquals(file_get_contents($fileName), $fileData);
            unlink($fileName);
        } catch (Exception $e) {
            if (file_exists($fileName)) {
                unlink($fileName);
            }
            throw $e;
        }
    }

    /**
     * @return array
     */
    public function writeDataProvider()
    {
        $filesPath = __DIR__ . DS . '..' . DS . '_files' . DS;
        return array(
            'correct file' => array($filesPath . 'tempFile.css', 'temporary data'),
            'empty file' => array($filesPath . 'tempFile2.css', '')
        );
    }

    public function testDelete()
    {
        $fileName = __DIR__ . DS . '..' . DS . '_files' . DS . 'tempFile3.css';
        file_put_contents($fileName, 'test data');
        try {
            $this->_adapter->delete($fileName);
            $this->assertFileNotExists($fileName);
        } catch (Exception $e) {
            unlink($fileName);
            throw $e;
        }
    }

    /**
     * @param string $sourceName
     * @param string $targetName
     * @throws Exception
     * @dataProvider renameDaraProvider
     */
    public function testRename($sourceName, $targetName)
    {
        try {
            file_put_contents($sourceName, 'test data');
            $this->_adapter->rename($sourceName, $targetName);
            $this->assertFileExists($targetName);
            $this->assertFileNotExists($sourceName);
            $this->assertEquals(file_get_contents($targetName), 'test data');
            unlink($targetName);
        } catch (Exception $e) {
            unlink($sourceName);
            unlink($targetName);
            throw $e;
        }
    }

    /**
     * @return array
     */
    public function renameDaraProvider()
    {
        $filesPath = __DIR__ . DS . '..' . DS . '_files' . DS;
        return array(
            'test 1' => array($filesPath . 'file1.js', $filesPath . 'file2.js'),
        );
    }

    public function testIsDirectory()
    {
        $filesPath = __DIR__ . DS . '..' . DS . '_files' . DS;
        $this->assertTrue($this->_adapter->isDirectory($filesPath));
        $this->assertFalse($this->_adapter->isDirectory($filesPath . 'popup.csv'));
    }

    public function testCreateDirectory()
    {
        $directoryName = __DIR__ . DS . '..' . DS . '_files' . DS . 'new_directory';
        if (file_exists($directoryName)) {
            rmdir($directoryName);
        }
        $this->_adapter->createDirectory($directoryName, 0111);
        $this->assertFileExists($directoryName);
        $this->assertTrue(is_dir($directoryName));
        unlink($directoryName);
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
     * @throws Exception
     */
    public function testTouch($fileName, $newFile = false)
    {
        try {
            if ($newFile) {
                $this->assertFileNotExists($fileName);
            } else {
                $this->assertFileExists($fileName);
            }
            $this->_adapter->touch($fileName);
            if ($newFile) {
                $this->assertFileExists($fileName);
                unlink($fileName);
            }
        } catch (Exception $e) {
            if ($newFile && file_exists($fileName)) {
                unlink($fileName);
            }
            throw $e;
        }
    }

    /**
     * @return array
     */
    public function touchDataProvider()
    {
        $filesPath = __DIR__ . DS . '..' . DS . '_files' . DS;
        return array(
            'update file' => array($filesPath . 'popup.csv', false),
            'create file' => array($filesPath . 'popup2.css', true)
        );
    }

    /**
     * @param string $sourceName
     * @param string $targetName
     * @throws Exception
     * @dataProvider renameDaraProvider
     */
    public function testCopy($sourceName, $targetName)
    {
        if (file_exists($sourceName)) {
            unlink($sourceName);
        }
        if (file_exists($targetName)) {
            unlink($targetName);
        }
        $testData = 'test data';
        file_put_contents($sourceName, $testData);
        $this->_adapter->copy($sourceName, $targetName);
        $this->assertFileExists($targetName);
        $this->assertFileExists($sourceName);
        $this->assertEquals($testData, file_get_contents($targetName));
        $this->assertEquals($testData, file_get_contents($targetName));
    }
}
