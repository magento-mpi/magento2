<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento;

use Magento\App\Filesystem as AppFilesystem;

class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    /** @var Filesystem */
    protected $_filesystem;

    /** @var \Magento\Filesystem\Directory\ReadFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $_dirReadFactoryMock;

    /** @var \Magento\Filesystem\Directory\WriteFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $_dirWriteFactoryMock;

    /** @var \Magento\App\Filesystem\DirectoryList|\PHPUnit_Framework_MockObject_MockObject  */
    protected $_directoryListMock;

    /** @var \Magento\Filesystem\File\ReadFactory|\PHPUnit_Framework_MockObject_MockObject  */
    protected $_fileReadFactoryMock;

    public function setUp()
    {
        $this->_dirReadFactoryMock = $this->getMock(
            'Magento\Filesystem\Directory\ReadFactory',
            array(),
            array(),
            '',
            false
        );
        $this->_directoryListMock = $this->getMock(
            'Magento\App\Filesystem\DirectoryList',
            array(),
            array(),
            '',
            false
        );
        $this->_dirWriteFactoryMock = $this->getMock(
            'Magento\Filesystem\Directory\WriteFactory',
            array(),
            array(),
            '',
            false
        );
        $this->_fileReadFactoryMock = $this->getMock(
            'Magento\Filesystem\File\ReadFactory',
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
        /** @var \Magento\Filesystem\Directory\ReadInterface $dirReadMock */
        $dirReadMock = $this->getMock('Magento\Filesystem\Directory\ReadInterface');
        $this->_dirReadFactoryMock->expects($this->once())->method('create')->will($this->returnValue($dirReadMock));
        $this->assertEquals($dirReadMock, $this->_filesystem->getDirectoryRead(AppFilesystem::ROOT_DIR));
    }

    /**
     * @expectedException \Magento\Filesystem\FilesystemException
     */
    public function testGetDirectoryWriteReadOnly()
    {
        $this->_setupDirectoryListMock(array('read_only' => true));
        $this->_filesystem->getDirectoryWrite(AppFilesystem::ROOT_DIR);
    }

    public function testGetDirectoryWrite()
    {
        $this->_setupDirectoryListMock(array());
        /** @var \Magento\Filesystem\Directory\WriteInterface $dirWriteMock */
        $dirWriteMock = $this->getMock('Magento\Filesystem\Directory\WriteInterface');
        $this->_dirWriteFactoryMock->expects($this->once())->method('create')->will($this->returnValue($dirWriteMock));
        $this->assertEquals($dirWriteMock, $this->_filesystem->getDirectoryWrite(AppFilesystem::ROOT_DIR));
    }

    public function testGetRemoteResource()
    {
        $fileReadMock = $this->getMock('Magento\Filesystem\File\ReadInterface', array(), array(), '', false);

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
        $this->assertEquals($uri, $this->_filesystem->getUri(AppFilesystem::ROOT_DIR));
    }

    protected function _setupDirectoryListMock(array $config)
    {
        $this->_directoryListMock->expects($this->any())->method('getConfig')->will($this->returnValue($config));
    }
}
