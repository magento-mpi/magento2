<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento;

class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    /** @var Filesystem */
    protected $_filesystem;

    /** @var \Magento\Filesystem\Directory\ReadFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $_dirReadFactoryMock;

    /** @var \Magento\Filesystem\Directory\WriteFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $_dirWriteFactoryMock;

    /** @var \Magento\Filesystem\DirectoryList|\PHPUnit_Framework_MockObject_MockObject  */
    protected $_directoryListMock;

    /** @var \Magento\Filesystem\File\ReadFactory|\PHPUnit_Framework_MockObject_MockObject  */
    protected $_fileReadFactoryMock;

    public function setUp()
    {
        $this->_dirReadFactoryMock = $this->getMock('Magento\Filesystem\Directory\ReadFactory', [], [], '', false);
        $this->_directoryListMock = $this->getMock('Magento\Filesystem\DirectoryList', [], [], '', false);
        $this->_dirWriteFactoryMock = $this->getMock('Magento\Filesystem\Directory\WriteFactory', [], [], '', false);
        $this->_fileReadFactoryMock = $this->getMock('Magento\Filesystem\File\ReadFactory', [], [], '', false);

        $this->_filesystem = new Filesystem(
            $this->_directoryListMock,
            $this->_dirReadFactoryMock,
            $this->_dirWriteFactoryMock,
            $this->_fileReadFactoryMock
        );
    }

    public function testGetDirectoryRead()
    {
        $this->_setupDirectoryListMock([]);
        /** @var \Magento\Filesystem\Directory\ReadInterface $dirReadMock */
        $dirReadMock = $this->getMock('Magento\Filesystem\Directory\ReadInterface');
        $this->_dirReadFactoryMock->expects($this->once())->method('create')->will($this->returnValue($dirReadMock));
        $this->assertEquals($dirReadMock, $this->_filesystem->getDirectoryRead(Filesystem::ROOT));
    }

    /**
     * @expectedException \Magento\Filesystem\FilesystemException
     */
    public function testGetDirectoryWriteReadOnly()
    {
        $this->_setupDirectoryListMock(['read_only' => true]);
        $this->_filesystem->getDirectoryWrite(Filesystem::ROOT);
    }

    public function testGetDirectoryWrite()
    {
        $this->_setupDirectoryListMock([]);
        /** @var \Magento\Filesystem\Directory\WriteInterface $dirWriteMock */
        $dirWriteMock = $this->getMock('Magento\Filesystem\Directory\WriteInterface');
        $this->_dirWriteFactoryMock->expects($this->once())->method('create')->will($this->returnValue($dirWriteMock));
        $this->assertEquals($dirWriteMock, $this->_filesystem->getDirectoryWrite(Filesystem::ROOT));
    }

    public function testGetRemoteResource()
    {
        $fileReadMock = $this->getMock('Magento\Filesystem\File\ReadInterface', [], [], '', false);

        $this->_fileReadFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with('example.com', 'http')
            ->will($this->returnValue($fileReadMock));

        $this->assertEquals($fileReadMock, $this->_filesystem->getRemoteResource('http://example.com'));
    }

    public function testGetPath()
    {
        $this->_setupDirectoryListMock(['path' => '\\some\\path']);
        $this->assertEquals('/some/path', $this->_filesystem->getPath(Filesystem::ROOT));
    }

    public function testGetUri()
    {
        $uri = 'http://example.com';
        $this->_setupDirectoryListMock(['uri' => $uri]);
        $this->assertEquals($uri, $this->_filesystem->getUri(Filesystem::ROOT));
    }

    protected function _setupDirectoryListMock(array $config)
    {
        $this->_directoryListMock
            ->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($config));
    }
}
