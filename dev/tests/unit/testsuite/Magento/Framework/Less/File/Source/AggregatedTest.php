<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Less\File\Source;

/**
 * Tests Aggregate
 */
class AggregatedTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\Framework\View\Layout\File\FileList\Factory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $fileListFactoryMock;

    /**
     * @var \Magento\Framework\View\Layout\File\FileList|PHPUnit_Framework_MockObject_MockObject
     */
    protected $fileListMock;

    /**
     * @var \Magento\Framework\View\Layout\File\SourceInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $libraryFilesMock;

    /**
     * @var \Magento\Framework\View\Layout\File\SourceInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $baseFilesMock;

    /**
     * @var \Magento\Framework\View\Layout\File\SourceInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $themeFilesMock;

    /**
     * @var \Magento\Framework\View\Design\ThemeInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $themeMock;

    /**
     * Setup tests
     * @return void
     */
    public function setup()
    {
        $this->fileListFactoryMock = $this->getMockBuilder('Magento\Framework\View\Layout\File\FileList\Factory')
            ->disableOriginalConstructor()->getMock();
        $this->fileListMock = $this->getMockBuilder('Magento\Framework\View\Layout\File\FileList')
            ->disableOriginalConstructor()->getMock();
        $this->fileListFactoryMock->expects($this->any())->method('create')
            ->will($this->returnValue($this->fileListMock));
        $this->libraryFilesMock = $this->getMockBuilder('Magento\Framework\View\Layout\File\SourceInterface')
            ->getMock();

        $this->baseFilesMock = $this->getMockBuilder('Magento\Framework\View\Layout\File\SourceInterface')->getMock();
        $this->themeFilesMock = $this->getMockBuilder('Magento\Framework\View\Layout\File\SourceInterface')->getMock();
        $this->themeMock = $this->getMockBuilder('\Magento\Framework\View\Design\ThemeInterface')->getMock();
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage magento_import returns empty result by path
     * @return void
     */
    public function testGetFilesEmpty()
    {
        $this->libraryFilesMock->expects($this->any())->method('getFiles')->will($this->returnValue([]));
        $this->baseFilesMock->expects($this->any())->method('getFiles')->will($this->returnValue([]));
        $this->themeFilesMock->expects($this->any())->method('getFiles')->will($this->returnValue([]));

        $aggregated = new Aggregated(
            $this->fileListFactoryMock,
            $this->libraryFilesMock,
            $this->baseFilesMock,
            $this->themeFilesMock
        );

        $this->themeMock->expects($this->any())->method('getInheritedThemes')->will($this->returnValue([]));

        $aggregated->getFiles($this->themeMock);
    }

    /**
     *
     * @dataProvider getFilesDataProvider
     *
     * @param $libraryFiles array Files in lib directory
     * @param $baseFiles array Files in base directory
     * @param $themeFiles array Files in theme
     * *
     * @return void
     */
    public function testGetFiles($libraryFiles, $baseFiles, $themeFiles)
    {
        $this->fileListMock->expects($this->at(0))->method('add')->with($this->equalTo($libraryFiles));
        $this->fileListMock->expects($this->at(1))->method('add')->with($this->equalTo($baseFiles));
        $this->fileListMock->expects($this->any())->method('getAll')->will($this->returnValue(['returnedFile']));

        $this->libraryFilesMock->expects($this->any())->method('getFiles')->will($this->returnValue($libraryFiles));
        $this->baseFilesMock->expects($this->any())->method('getFiles')->will($this->returnValue($baseFiles));

        $this->themeFilesMock->expects($this->any())->method('getFiles')->will($this->returnValue($themeFiles));

        $aggregated = new Aggregated(
            $this->fileListFactoryMock,
            $this->libraryFilesMock,
            $this->baseFilesMock,
            $this->themeFilesMock
        );

        $inheritedThemeMock = $this->getMockBuilder('\Magento\Framework\View\Design\ThemeInterface')->getMock();
        $this->themeMock->expects($this->any())->method('getInheritedThemes')
            ->will($this->returnValue([$inheritedThemeMock]));

        $aggregated->getFiles($this->themeMock);
    }

    /**
     * Provides test data for testGetFiles()
     *
     * @return array
     */
    public function getFilesDataProvider()
    {
        return [
            'all files' => [['file1'], ['file2'], ['file3']],
            'no library' => [[], ['file1', 'file2'], ['file3']],
        ];
    }
}