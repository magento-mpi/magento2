<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework;

use Magento\Framework\App\Filesystem\DirectoryList;

class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    /** @var Filesystem */
    protected $_filesystem;

    /** @var \Magento\Framework\Filesystem\Directory\ReadFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $_dirReadFactoryMock;

    /** @var \Magento\Framework\Filesystem\Directory\WriteFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $_dirWriteFactoryMock;

    /** @var \Magento\Framework\App\Filesystem\DirectoryList|\PHPUnit_Framework_MockObject_MockObject  */
    protected $_directoryListMock;

    /** @var \Magento\Framework\Filesystem\File\ReadFactory|\PHPUnit_Framework_MockObject_MockObject  */
    protected $_fileReadFactoryMock;

    public function setUp()
    {
        $this->_dirReadFactoryMock = $this->getMock(
            'Magento\Framework\Filesystem\Directory\ReadFactory',
            array(),
            array(),
            '',
            false
        );
        $this->_directoryListMock = $this->getMock(
            'Magento\Framework\App\Filesystem\DirectoryList',
            array(),
            array(),
            '',
            false
        );
        $this->_dirWriteFactoryMock = $this->getMock(
            'Magento\Framework\Filesystem\Directory\WriteFactory',
            array(),
            array(),
            '',
            false
        );
        $this->_fileReadFactoryMock = $this->getMock(
            'Magento\Framework\Filesystem\File\ReadFactory',
            array(),
            array(),
            '',
            false
        );

        $this->_filesystem = new Filesystem(
            $this->_directoryListMock,
            $this->_dirReadFactoryMock,
            $this->_dirWriteFactoryMock,
            $this->_fileReadFactoryMock
        );
    }

    public function testGetDirectoryRead()
    {
        $this->_setupDirectoryListMock(array());
        /** @var \Magento\Framework\Filesystem\Directory\ReadInterface $dirReadMock */
        $dirReadMock = $this->getMock('Magento\Framework\Filesystem\Directory\ReadInterface');
        $this->_dirReadFactoryMock->expects($this->once())->method('create')->will($this->returnValue($dirReadMock));
        $this->assertEquals($dirReadMock, $this->_filesystem->getDirectoryRead(DirectoryList::ROOT));
    }

    /**
     * @expectedException \Magento\Framework\Filesystem\FilesystemException
     */
    public function testGetDirectoryWriteReadOnly()
    {
        $this->_setupDirectoryListMock(array('read_only' => true));
        $this->_filesystem->getDirectoryWrite(DirectoryList::ROOT);
    }

    public function testGetDirectoryWrite()
    {
        $this->_setupDirectoryListMock(array());
        /** @var \Magento\Framework\Filesystem\Directory\WriteInterface $dirWriteMock */
        $dirWriteMock = $this->getMock('Magento\Framework\Filesystem\Directory\WriteInterface');
        $this->_dirWriteFactoryMock->expects($this->once())->method('create')->will($this->returnValue($dirWriteMock));
        $this->assertEquals($dirWriteMock, $this->_filesystem->getDirectoryWrite(DirectoryList::ROOT));
    }

    public function testGetRemoteResource()
    {
        $fileReadMock = $this->getMock('Magento\Framework\Filesystem\File\ReadInterface', array(), array(), '', false);

        $this->_fileReadFactoryMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            'example.com',
            'http'
        )->will(
            $this->returnValue($fileReadMock)
        );

        $this->assertEquals($fileReadMock, $this->_filesystem->getRemoteResource('http://example.com'));
    }

    public function testGetUri()
    {
        $uri = 'http://example.com';
        $this->_setupDirectoryListMock(array('uri' => $uri));
        $this->assertEquals($uri, $this->_filesystem->getUri(DirectoryList::ROOT));
    }

    protected function _setupDirectoryListMock(array $config)
    {
        $this->_directoryListMock->expects($this->any())->method('getConfig')->will($this->returnValue($config));
    }
}
