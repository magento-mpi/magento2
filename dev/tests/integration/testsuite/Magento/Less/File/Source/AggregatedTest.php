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
    /**
     * @var \Magento\Less\File\Source\Aggregated
     */
    protected $model;
    protected $theme;


    public function setUp()
    {
        $this->theme = $this->_getThemeMock();
        $this->model = $this->_getModelMock($this->theme);
    }

    public function testGetFiles()
    {
        $files = $this->model->getFiles('*', $this->theme);

        /** @var $file \Magento\View\Layout\File */
        foreach ($files as $file) {
            echo $file->getFilename().PHP_EOL;
        }
    }

    protected function _getThemeMock()
    {
        $grandParentTheme = $this->getMockBuilder('Magento\Core\Model\Theme')
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
     * @param \Magento\Core\Model\Theme $theme
     * @return Aggregated
     */
    protected function _getModelMock(\Magento\Core\Model\Theme $theme)
    {
        // 1. mock File\Source\Library
        $libraryFiles = $this->getMockBuilder('Magento\Less\File\Source\Library')
            ->disableOriginalConstructor()
            ->setMethods(array('getFiles'))
            ->getMock();
        $libraryFiles->expects($this->any())
            ->method('getFiles')
            ->will($this->returnValue(array(new \Magento\View\Layout\File('library.file', 'Magento_Module'))));

        // 2. mock File\Source\Base
        $baseFiles = $this->getMockBuilder('Magento\Less\File\Source\Base')
            ->disableOriginalConstructor()
            ->setMethods(array('getFiles'))
            ->getMock();
        $baseFiles->expects($this->any())
            ->method('getFiles')
            ->will($this->returnValue(array(new \Magento\View\Layout\File('base.file', 'Magento_Module'))));

        // 3. mock File\Source\Theme
        $themeFiles = $this->getMockBuilder('Magento\Less\File\Source\Theme')
            ->disableOriginalConstructor()
            ->setMethods(array('getFiles'))
            ->getMock();
        $parentTheme = $theme->getParentTheme();
        $grandParentTheme = $parentTheme->getParentTheme();
        $themeFiles->expects($this->any())
            ->method('getFiles')
            ->will(
            //$this->returnValue(123)
            $this->returnValueMap(array(
                array($theme, '*', array(new \Magento\View\Layout\File('theme.file', 'Magento_Module', $theme))),
                array($parentTheme, '*',
                    array(new \Magento\View\Layout\File('parent.theme.file', 'Magento_Module', $parentTheme))
                ),
                array($grandParentTheme, '*',
                    array(new \Magento\View\Layout\File('grand.parent.theme.file', 'Magento_Module', $grandParentTheme)),
                    array(new \Magento\View\Layout\File('grand.parent.theme.file', 'Magento_Module', $grandParentTheme))
                ),
            )));


        var_dump(get_class($theme));


        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $model = $objectManager->create('Magento\Less\File\Source\Aggregated', array(
            'libraryFiles' => $libraryFiles,
            'baseFiles'    => $baseFiles,
            'themeFiles'   => $themeFiles
        ));

        return $model;
    }
}
