<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Less\File\Source;

class AggregatedTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFiles()
    {
        $this->markTestIncomplete('Will be fixed in MAGETWO-19245');

        $model = $this->_getModelMock();
        $theme = $this->_getThemeMock();

        $result = $model->getFiles($theme);
    }

    protected function _getThemeMock()
    {
        $grandParentTheme = $this->getMockBuilder('Magento\Core\Model\Theme') //Magento\View\Design\Theme
            ->disableOriginalConstructor()
            ->setMethods(array('getParentTheme', '__wakeup'))
            ->getMock();
        $grandParentTheme->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue(null));

        $parentTheme = $this->getMockBuilder('Magento\Core\Model\Theme') //Magento\View\Design\Theme
            ->disableOriginalConstructor()
            ->setMethods(array('getParentTheme', '__wakeup'))
            ->getMock();
        $parentTheme->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue($grandParentTheme));

        $theme = $this->getMockBuilder('Magento\Core\Model\Theme') //Magento\View\Design\Theme
            ->disableOriginalConstructor()
            ->setMethods(array('getParentTheme', '__wakeup'))
            ->getMock();
        $theme->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue($parentTheme));



        return $theme;
    }

    /**
     * @return Aggregated
     */
    protected function _getModelMock()
    {
        // 1. Mock FileList\Factory
        $fileList = $this->getMockBuilder('Magento\Less\File\FileList')
            ->disableOriginalConstructor()
            ->setMethods(array('add', 'replace', 'getAll'))
            ->getMock();
        $fileList->expects($this->any())
            ->method('getAll')
            ->will($this->returnValue(123));

        $factory = $this->getMockBuilder('Magento\Less\File\FileList\Factory')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMock();
        $factory->expects($this->any())->method('create')->will($this->returnValue($fileList));

        // 2. mock File\Source\Base
        $baseFiles = $this->getMockBuilder('Magento\Less\File\Source\Base')
            ->disableOriginalConstructor()
            ->setMethods(array('getFiles'))
            ->getMock();
        $baseFiles->expects($this->any())
            ->method('getFiles')
            ->will($this->returnValue(array()));

        // 3. mock File\Source\Theme
        $themeFiles = $this->getMockBuilder('Magento\Less\File\Source\Theme')
            ->disableOriginalConstructor()
            ->setMethods(array('getFiles'))
            ->getMock();
        $themeFiles->expects($this->any())
            ->method('getFiles')
            ->will($this->returnValue(array()));

        // 4. other mocks
        $mock1 = $this->getMockBuilder('Magento\Less\File\SourceInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getFiles'))
            ->getMock();
        $mock1->expects($this->any())
            ->method('getFiles')
            ->will($this->returnValue(array()));
        $mock2 = $this->getMockBuilder('Magento\Less\File\SourceInterface')
            ->disableOriginalConstructor()
            ->setMethods(array('getFiles'))
            ->getMock();
        $mock2->expects($this->any())
            ->method('getFiles')
            ->will($this->returnValue(array()));

        $model = new \Magento\Less\File\Source\Aggregated($factory, $baseFiles, $themeFiles, $mock1, $mock2);

        return $model;
    }
}
