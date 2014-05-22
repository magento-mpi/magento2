<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Less\File\Source;

/**
 * Tests Base
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\Framework\App\Filesystem|PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystemMock;

    /**
     * @var \Magento\Framework\View\Layout\File\Factory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $fileFactoryMock;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $modulesDirectoryMock;

    /**
     * @var \Magento\Framework\View\Design\ThemeInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $themeMock;

    public function setup()
    {
        $this->filesystemMock = $this->getMockBuilder('Magento\Framework\App\Filesystem')
            ->disableOriginalConstructor()->getMock();

        $this->modulesDirectoryMock = $this->getMockBuilder('Magento\Framework\Filesystem\Directory\ReadInterface')
            ->getMock();
        $this->filesystemMock->expects($this->any())->method('getDirectoryRead')
            ->will($this->returnValue($this->modulesDirectoryMock));

        $this->fileFactoryMock = $this->getMockBuilder('Magento\Framework\View\Layout\File\Factory')
            ->disableOriginalConstructor()->getMock();

        $this->themeMock = $this->getMockBuilder('Magento\Framework\View\Design\ThemeInterface')->getMock();
    }

    public function testGetFilesEmpty()
    {
        $this->modulesDirectoryMock->expects($this->any())->method('search')->will($this->returnValue([]));
        $base = new Base(
            $this->filesystemMock,
            $this->fileFactoryMock
        );

        $base->getFiles($this->themeMock);
    }

    public function testGetFilesSingle()
    {
        $filePath = 'Magento/Customer/view/frontend/something.less';
        $this->modulesDirectoryMock->expects($this->once())
            ->method('search')
            ->will($this->returnValue(['file']));
        $this->modulesDirectoryMock->expects($this->once())
            ->method('getAbsolutePath')
            ->with('file')
            ->will($this->returnValue($filePath));

        $this->themeMock->expects($this->any())->method('getArea')->will($this->returnValue('frontend'));

        $fileMock = $this->getMockBuilder('Magento\Framework\View\Layout\File')
            ->setConstructorArgs(['file1', 'Magento_Customer'])
            ->getMock();

        // Verifies Magento_Customer was correctly produced from directory path
        $this->fileFactoryMock->expects($this->once())
            ->method('create')
            ->with($this->equalTo($filePath), $this->equalTo('Magento_Customer'))
            ->will($this->returnValue($fileMock));

        $base = new Base(
            $this->filesystemMock,
            $this->fileFactoryMock
        );
        $this->assertEquals([$fileMock], $base->getFiles($this->themeMock));
    }

    public function testGetFilesMultiple()
    {

        $dirPath = 'Magento/Customer/view/frontend/';
        $this->modulesDirectoryMock->expects($this->once())
            ->method('search')
            ->will($this->returnValue(['fileA.test', 'fileB.tst', 'fileC.test']));
        $this->modulesDirectoryMock->expects($this->any())
            ->method('getAbsolutePath')
            ->will(
                $this->returnValueMap(
                    [
                        ['fileA.test', $dirPath . 'fileA.test'],
                        ['fileB.tst', $dirPath . 'fileB.tst'],
                        ['fileC.test', $dirPath . 'fileC.test'],
                    ]
                )
            );

        $this->themeMock->expects($this->any())->method('getArea')->will($this->returnValue('frontend'));

        $fileMock = $this->getMockBuilder('Magento\Framework\View\Layout\File')->setConstructorArgs(['file1', 'Magento_Customer'])->getMock();

        // Verifies Magento_Customer was correctly produced from directory path
        $this->fileFactoryMock->expects($this->any())
            ->method('create')
            ->with($this->isType('string'), $this->equalTo('Magento_Customer'))
            ->will($this->returnValue($fileMock));

        $base = new Base(
            $this->filesystemMock,
            $this->fileFactoryMock
        );
        // Only two files should be in array, because fileB.tst won't match *.test
        $this->assertEquals([$fileMock, $fileMock], $base->getFiles($this->themeMock, '*.test'));
    }

}