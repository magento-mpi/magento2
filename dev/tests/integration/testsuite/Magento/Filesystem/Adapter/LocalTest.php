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
            'existed file' => array($filesPath . 'popup.css', true),
            'not existed file' => array($filesPath . 'popup2.css', false),
        );
    }

    /**
     * @param string $fileName
     * @param string $expectedContent
     * @param bool $success
     * @dataProvider readDataProvider
     */
    public function testRead($fileName, $expectedContent, $success)
    {
        if ($success) {
            $this->assertEquals($expectedContent, $this->_adapter->read($fileName));
        } else {
            $this->assertNotEquals($expectedContent, $this->_adapter->read($fileName));
        }
    }

    /**
     * @return array
     */
    public function readDataProvider()
    {
        $filesPath = __DIR__ . DS . '..' . DS . '_files' . DS;
        return array(
            'correct read' => array($filesPath . 'popup.css', 'var myData = 5;', true),
            'incorrect read' => array($filesPath . 'popup.css', 'var myData = 5; ', false),
            'not existed file' => array($filesPath . 'popup2.css', false, true)
        );
    }

    /**
     * @param string $fileName
     * @param string $fileData
     * @param bool $success
     * @dataProvider writeDataProvider
     * @throws Exception
     */
    public function testWrite($fileName, $fileData, $success)
    {
        try {
            $this->_adapter->write($fileName, $fileData);
            if ($success) {
                $this->assertFileExists($fileName);
                $this->assertEquals(file_get_contents($fileName), $fileData);
            } else {
                $this->assertFileNotExists($fileName);
            }
            unlink($fileName);
        } catch (Exception $e) {
            unlink($fileName);
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
            'correct file' => array($filesPath . 'tempFile.css', 'temporary data', true),
            'empty file' => array($filesPath . 'tempFile2.css', '', true),
            'incorrect path' => array('', 'temporary data', false)
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
        $this->assertFalse($this->_adapter->isDirectory($filesPath . 'popup.css'));
    }

    /**
     * @param string $directoryName
     * @param bool $success
     * @dataProvider createDirectoryDataProvider
     * @throws Exception
     */
    public function jtestCreateDirectory($directoryName, $success)
    {
        try{
            if ($success) {
                $this->assertTrue($this->_adapter->createDirectory($directoryName));
                $this->assertFileExists($directoryName);
                $this->assertTrue(is_dir($directoryName));
                unlink($directoryName);
            } else {
                $this->assertFalse($this->_adapter->createDirectory($directoryName));
                $this->assertFileNotExists($directoryName);
            }
        } catch (Exception $e) {
            unlink($directoryName);
            throw $e;
        }
    }

    /**
     * @return array
     */
    public function createDirectoryDataProvider()
    {
        return array(
            'success' => array(__DIR__ . DS . '..' . DS . '_files' . DS . 'new_directory', true),
            'failure' => array('', false),
        );
    }

    public function testTouch($fileName, $success, $delete = false)
    {
        try {
            $this->assertEquals($success, $this->_adapter->touch($fileName));
            if ($success) {
                $this->assertFileExists($fileName);
                $this->assertGreaterThan(microtime(), filemtime($fileName) + 10);
            }
            if ($delete) {
                unlink($fileName);
            }
        } catch (Exception $e) {
            if ($delete) {
                unlink($fileName);
            }
            throw $e;
        }
    }

    public function touchDataProvider()
    {
        $filesPath = __DIR__ . DS . '..' . DS . '_files' . DS;
        return array(
            'update file' => array($filesPath . 'popup.css', true),
            'create file' => array($filesPath . 'popup2.css', true, true),
            'wrong file' => array($filesPath, false),
        );
    }
}
