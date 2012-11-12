<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Index
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Index_Model_Lock_StorageTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test var directory
     *
     * @var string
     */
    protected $_testVarDirectory = 'test';

    /**
     * Locks storage model
     *
     * @var Mage_Index_Model_Lock_Storage
     */
    protected $_storage;

    /**
     * Keep current process id for tests
     *
     * @var integer
     */
    protected $_currentProcessId;

    /**
     * Is fileExists method called flag
     *
     * @var bool
     */
    protected $_isFileExistsCalled = false;

    protected function setUp()
    {
        $config = $this->getMock('Mage_Core_Model_Config', array('getVarDir'), array(), '', false);
        $config->expects($this->exactly(2))
            ->method('getVarDir')
            ->will($this->returnValue($this->_testVarDirectory));

        $fileModel = $this->getMock('Mage_Index_Model_Process_File',
            array(
                'fileExists',
                'mkdir',
                'cd',
                'streamOpen',
                'streamWrite',
            )
        );

        $fileModel->expects($this->exactly(2))
            ->method('fileExists')
            ->will($this->returnCallback(array($this, 'isFileExistsCallback')));
        $fileModel->expects($this->once())
            ->method('mkdir')
            ->will($this->returnCallback(array($this, 'checkVarDirectoryCallback')));
        $fileModel->expects($this->exactly(2))
            ->method('cd')
            ->will($this->returnCallback(array($this, 'checkVarDirectoryCallback')));
        $fileModel->expects($this->exactly(2))
            ->method('streamOpen')
            ->will($this->returnCallback(array($this, 'checkFilenameCallback')));
        $fileModel->expects($this->exactly(2))
            ->method('streamWrite')
            ->will($this->returnCallback(array($this, 'checkStreamContentCallback')));

        $fileFactory = $this->getMock('Mage_Index_Model_Process_FileFactory', array('createFromArray'), array(), '',
            false
        );
        $fileFactory->expects($this->exactly(2))
            ->method('createFromArray')
            ->will($this->returnValue($fileModel));

        $this->_storage = new Mage_Index_Model_Lock_Storage($config, $fileFactory);
    }

    public function testGetFile()
    {
        $processIdList = array(1, 2, 2);
        foreach ($processIdList as $processId) {
            $this->_currentProcessId = $processId;
            $this->assertInstanceOf('Mage_Index_Model_Process_File', $this->_storage->getFile($processId));
        }
    }

    /**
     * First time this method will return false, all other times true
     * In this way we check two cases of fileExists behavior
     *
     * @param string $directory
     * @return bool
     */
    public function isFileExistsCallback($directory)
    {
        $this->checkVarDirectoryCallback($directory);

        if ($this->_isFileExistsCalled) {
            return true;
        } else {
            $this->_isFileExistsCalled = true;
            return false;
        }
    }

    /**
     * Check path to var directory
     * First time this method will return false, all other times true
     * In this way we check two cases of fileExists behavior
     *
     * @param string $directory
     */
    public function checkVarDirectoryCallback($directory)
    {
        $this->assertEquals($this->_testVarDirectory, $directory);
    }

    /**
     * Check file name
     *
     * @param string $filename
     */
    public function checkFilenameCallback($filename)
    {
        $expected = 'index_process_' . $this->_currentProcessId . '.lock';
        $this->assertEquals($expected, $filename);
    }

    /**
     * Check stream content data
     *
     * @param string $data
     */
    public function checkStreamContentCallback($data)
    {
        $this->assertEquals(date('r'), $data);
    }
}
