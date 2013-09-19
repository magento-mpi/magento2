<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\File\Storage;

class SynchronizationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\File\Storage\Synchronization
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storageMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storageFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_streamMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_streamFactoryMock;

    /**
     * @var string
     */
    protected $_content = 'content';

    protected function setUp()
    {
        $this->_storageFactoryMock =
            $this->getMock('Magento\Core\Model\File\Storage\DatabaseFactory', array('create'), array(), '', false);
        $this->_storageMock = $this->getMock('Magento\Core\Model\File\Storage\Database',
                array('getContent', 'getId', 'loadByFilename'), array(), '', false);
        $this->_storageFactoryMock
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->_storageMock));

        $this->_storageMock->expects($this->once())->method('getContent')->will($this->returnValue($this->_content));
        $this->_streamFactoryMock =
            $this->getMock('Magento\Filesystem\Stream\LocalFactory', array('create'), array(), '', false);
        $this->_streamMock = $this->getMock('Magento\Filesystem\StreamInterface');
        $this->_streamFactoryMock
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_streamMock));

        $this->_model = new \Magento\Core\Model\File\Storage\Synchronization(
                        $this->_storageFactoryMock, $this->_streamFactoryMock);
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_storageMock);
    }

    public function testSynchronize()
    {
        $relativeFileName = 'config.xml';
        $filePath = realpath(__DIR__ . '/_files/');
        $this->_storageMock->expects($this->once())->method('getId')->will($this->returnValue(true));
        $this->_storageMock->expects($this->once())->method('loadByFilename');
        $this->_streamMock->expects($this->once())->method('open')->with('w');
        $this->_streamMock->expects($this->once())->method('write')->with($this->_content);
        $this->_model->synchronize($relativeFileName, $filePath);
    }
}
