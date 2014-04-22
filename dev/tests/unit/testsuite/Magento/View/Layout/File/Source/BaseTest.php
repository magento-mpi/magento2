<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\File\Source;

/**
 * Class for testing Magento\View\Layout\File\Source\Base
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Layout\File\Source\Base|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $class;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $modulesDirectoryMock;

    /**
     * @var \Magento\View\Layout\File\Factory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fileFactoryMock;

    /**
     * Set up mocks
     */
    public function setUp()
    {
        $filesystemMock = $this->getMock(
            'Magento\Framework\App\Filesystem',
            ['getDirectoryRead', 'getAbsolutePath'],
            [],
            '',
            false
        );
        $this->fileFactoryMock = $this->getMock('Magento\View\Layout\File\Factory', [], [], '', false);
        $this->modulesDirectoryMock = $this->getMock('Magento\Filesystem\Directory\ReadInterface', [], [], '', false);

        $filesystemMock->expects($this->once())
            ->method('getDirectoryRead')
            ->will($this->returnValue($this->modulesDirectoryMock));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->class = $objectManager->getObject(
            'Magento\View\Layout\File\Source\Base',
            [
                'filesystem' => $filesystemMock,
                'fileFactory' => $this->fileFactoryMock
            ]
        );
    }

    /**
     * Test for method getFiles
     */
    public function testGetFiles()
    {
        $fileName = 'somefile.xml';
        $fileRightPath = '/namespace/module/view/base/layout/' . $fileName;
        $themeFileRightPath = '/namespace/module/view/area_code/layout/' . $fileName;
        $fileWrongPath = '/namespace/module/view/' . $fileName;
        $themeFileWrongPath = '/namespace/module/view/area_code/' . $fileName;
        $areaCode = 'area_code';
        $sharedFiles = [
            $fileRightPath,
            $fileWrongPath,
        ];
        $themeFiles = [
            $themeFileRightPath,
            $themeFileWrongPath,
        ];
        $themeMock = $this->getMock('Magento\View\Design\ThemeInterface', [], [], '', false);

        $this->modulesDirectoryMock->expects($this->any())
            ->method('search')
            ->will($this->returnValueMap(
                    [
                        ["*/*/view/base/layout/*.xml", null, $sharedFiles],
                        ["*/*/view/$areaCode/layout/*.xml", null, $themeFiles],
                    ]
                ));
        $this->modulesDirectoryMock->expects($this->any())
            ->method('getAbsolutePath')
            ->will($this->returnArgument(0));
        $themeMock->expects($this->once())
            ->method('getArea')
            ->will($this->returnValue($areaCode));

        $this->fileFactoryMock->expects($this->at(0))
            ->method('create')
            ->with($this->equalTo($fileRightPath), $this->equalTo('namespace_module'))
            ->will($this->returnValue($fileRightPath));
        $this->fileFactoryMock->expects($this->at(1))
            ->method('create')
            ->with($this->equalTo($themeFileRightPath), $this->equalTo('namespace_module'))
            ->will($this->returnValue($themeFileRightPath));

        $expected = [$fileRightPath, $themeFileRightPath];
        $result = $this->class->getFiles($themeMock);
        $this->assertEquals($expected, $result);
    }
}
