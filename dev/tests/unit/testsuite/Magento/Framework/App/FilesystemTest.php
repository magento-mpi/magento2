<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

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

    public function testGetPath()
    {
        $this->_directoryListMock->expects($this->once())->method('getPath')->will($this->returnValue('\\some\\path'));
        $this->assertEquals('/some/path', $this->_filesystem->getPath(DirectoryList::ROOT));
    }
}
