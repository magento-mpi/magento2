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
namespace Magento\Index\Model\Lock;

class StorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Keep current process id for tests
     *
     * @var integer
     */
    protected $_callbackProcessId;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirsMock;

    public function testGetFile()
    {
        $streamMock = $this->getMockBuilder('Magento\Filesystem\File\Write')
            ->disableOriginalConstructor()
            ->getMock();

        $directoryMock = $this->getMockBuilder('Magento\Filesystem\Directory\Write')
            ->disableOriginalConstructor()
            ->getMock();
        $directoryMock->expects($this->exactly(2))
            ->method('create');

        $directoryMock->expects($this->any())
            ->method('openFile')
            ->will($this->returnValue($streamMock));

        $filesystemMock = $this->getMockBuilder('Magento\App\Filesystem')
            ->disableOriginalConstructor()
            ->getMock();
        $filesystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(\Magento\App\Filesystem::VAR_DIR)
            ->will($this->returnValue($directoryMock));

        $fileModel = $this->getMock('Magento\Index\Model\Process\File', array(), array($streamMock), '');

        $fileFactory = $this->getMock(
            'Magento\Index\Model\Process\FileFactory',
            array('create'),
            array($streamMock),
            '',
            false
        );
        $fileFactory->expects($this->exactly(2))
            ->method('create')
            ->will($this->returnValue($fileModel));

        $storage = new \Magento\Index\Model\Lock\Storage($fileFactory, $filesystemMock);

        /**
         * List if test process IDs.
         * We need to test cases when new ID and existed ID passed into tested method.
         */
        $processIdList = array(1, 2, 2);
        foreach ($processIdList as $processId) {
            $this->_callbackProcessId = $processId;
            $this->assertInstanceOf('Magento\Index\Model\Process\File', $storage->getFile($processId));
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
