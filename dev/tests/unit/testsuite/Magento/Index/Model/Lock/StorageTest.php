<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Index_Model_Lock_StorageTest extends PHPUnit_Framework_TestCase
{
    /**
     * Keep current process id for tests
     *
     * @var integer
     */
    protected $_callbackProcessId;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirsMock;

    public function testGetFile()
    {
        $this->_dirsMock = $this->getMock('Magento_Core_Model_Dir', array(), array(), '', false, false);
        $this->_dirsMock->expects($this->any())
            ->method('getDir')
            ->with(Magento_Core_Model_Dir::VAR_DIR)
            ->will($this->returnValue(__DIR__ . DIRECTORY_SEPARATOR. 'var'));

        $fileModel = $this->getMock('Magento_Index_Model_Process_File',
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
            ->with(array('path' => __DIR__  . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'locks'));
        $fileModel->expects($this->exactly(2))
            ->method('streamOpen')
            ->will($this->returnCallback(array($this, 'checkFilenameCallback')));
        $fileModel->expects($this->exactly(2))
            ->method('streamWrite')
            ->with($this->isType('string'));

        $fileFactory = $this->getMock('Magento_Index_Model_Process_FileFactory', array('create'), array(), '',
            false
        );
        $fileFactory->expects($this->exactly(2))
            ->method('create')
            ->will($this->returnValue($fileModel));

        $storage = new Magento_Index_Model_Lock_Storage($this->_dirsMock, $fileFactory);

        /**
         * List if test process IDs.
         * We need to test cases when new ID and existed ID passed into tested method.
         */
        $processIdList = array(1, 2, 2);
        foreach ($processIdList as $processId) {
            $this->_callbackProcessId = $processId;
            $this->assertInstanceOf('Magento_Index_Model_Process_File', $storage->getFile($processId));
        }
        $this->assertAttributeCount(2, '_fileHandlers', $storage);
    }

    /**
     * Check file name (callback subroutine for testGetFile())
     *
     * @param string $filename
     */
    public function checkFilenameCallback($filename)
    {
        $expected = 'index_process_' . $this->_callbackProcessId . '.lock';
        $this->assertEquals($expected, $filename);
    }
}
