<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

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

    public function testGetPath()
    {
        $this->_setupDirectoryListMock(array('path' => '\\some\\path'));
        $this->assertEquals('/some/path', $this->_filesystem->getPath(Filesystem::ROOT_DIR));
    }

    protected function _setupDirectoryListMock(array $config)
    {
        $this->_directoryListMock->expects($this->any())->method('getConfig')->will($this->returnValue($config));
    }
}
