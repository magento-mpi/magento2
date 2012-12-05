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

/**
 * Test for Mage_Index_Model_Lock_Storage
 */
class Mage_Index_Model_Lock_StorageTest extends PHPUnit_Framework_TestCase
{
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

    protected function setUp()
    {
        $basePath = 'base';
        $varDirectory = 'test';
        $dirs = new Mage_Core_Model_App_Dir(
            $basePath,
            array(Mage_Core_Model_App_Dir::VAR_DIR => $varDirectory)
        );

        $fileModel = $this->getMock('Mage_Index_Model_Process_File',
            array(
                'setAllowCreateFolders',
                'open',
                'streamOpen',
                'streamWrite',
            )
        );

        $fileModel->expects($this->exactly(2))
            ->method('setAllowCreateFolders')
            ->with(true);
        $fileModel->expects($this->exactly(2))
            ->method('open')
            ->with(array('path' => $basePath . DIRECTORY_SEPARATOR . $varDirectory . DIRECTORY_SEPARATOR . 'locks'));
        $fileModel->expects($this->exactly(2))
            ->method('streamOpen')
            ->will($this->returnCallback(array($this, 'checkFilenameCallback')));
        $fileModel->expects($this->exactly(2))
            ->method('streamWrite')
            ->with($this->isType('string'));

        $fileFactory = $this->getMock('Mage_Index_Model_Process_FileFactory', array('createFromArray'), array(), '',
            false
        );
        $fileFactory->expects($this->exactly(2))
            ->method('createFromArray')
            ->will($this->returnValue($fileModel));

        $this->_storage = new Mage_Index_Model_Lock_Storage($dirs, $fileFactory);
    }

    public function testGetFile()
    {
        /**
         * List if test process IDs.
         * We need to test cases when new ID and existed ID passed into tested method.
         */
        $processIdList = array(1, 2, 2);
        foreach ($processIdList as $processId) {
            $this->_currentProcessId = $processId;
            $this->assertInstanceOf('Mage_Index_Model_Process_File', $this->_storage->getFile($processId));
        }
        $this->assertAttributeCount(2, '_fileHandlers', $this->_storage);
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
}
