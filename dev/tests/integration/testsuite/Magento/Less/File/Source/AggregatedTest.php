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

    /**
     * @param string $path
     * @param \Magento\View\Layout\File[] $expectedFiles
     * @dataProvider getFilesDataProvider
     */
    public function testGetFiles($path, $expectedFiles)
    {
        $files = $this->model->getFiles($path, $this->theme);

        $this->assertEquals(count($expectedFiles), count($files), 'Files number doesn\'t match');

        /** @var $file \Magento\View\Layout\File */
        foreach ($files as $file) {
            if (!in_array($file->getFilename(), $expectedFiles)) {
                $this->fail(sprintf('File "%s" is not expected but found', $file->getFilename()));
            }
        }

        //var_dump($expectedFiles);
        //exit;
    }

    public function getFilesDataProvider()
    {
        return array(
            //array('*', array('library.file', 'module.file', '1.file', '2.file', '3.file')),
            //array('library.file', array('library.file')),
            //array('module.file', array('module.file')),
            array('1.file', array('1.file')),
        );
    }

    protected function _getThemeMock()
    {
        $grandParentTheme = $this->getMockBuilder('Magento\Core\Model\Theme')
            ->disableOriginalConstructor()
            ->setMethods(array('getParentTheme', 'getFullPath', '__wakeup'))
            ->getMock();
        $grandParentTheme->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue(null));
        $grandParentTheme->expects($this->any())
            ->method('getFullPath')
            ->will($this->returnValue('grandParentTheme'));

        $parentTheme = $this->getMockBuilder('Magento\Core\Model\Theme') //Magento\View\Design\Theme
            ->disableOriginalConstructor()
            ->setMethods(array('getParentTheme', 'getFullPath', '__wakeup'))
            ->getMock();
        $parentTheme->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue($grandParentTheme));
        $parentTheme->expects($this->any())
            ->method('getFullPath')
            ->will($this->returnValue('parentTheme'));

        $theme = $this->getMockBuilder('Magento\Core\Model\Theme') //Magento\View\Design\Theme
            ->disableOriginalConstructor()
            ->setMethods(array('getParentTheme', 'getFullPath', '__wakeup'))
            ->getMock();
        $theme->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue($parentTheme));
        $theme->expects($this->any())
            ->method('getFullPath')
            ->will($this->returnValue('theme'));



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
            ->will($this->returnValue(array(
                new \Magento\View\Layout\File('library.file', 'Magento_Module'),
            )));

        // 2. mock File\Source\Base
        $baseFiles = $this->getMockBuilder('Magento\Less\File\Source\Base')
            ->disableOriginalConstructor()
            ->setMethods(array('getFiles'))
            ->getMock();
        $baseFiles->expects($this->any())
            ->method('getFiles')
            ->will($this->returnValue(array(
                new \Magento\View\Layout\File('module.file', 'Magento_Module'),
            )));

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
                $this->returnCallback(
                    function ($filePath, $themeToSearch) use ($theme, $parentTheme, $grandParentTheme) {
                        $maps = array(
                            array('*', $theme, array(
                                new \Magento\View\Layout\File('3.file', 'Magento_Module', $theme)
                            )),
                            array('*', $parentTheme, array(
                                new \Magento\View\Layout\File('2.file', 'Magento_Module', $parentTheme),
                                new \Magento\View\Layout\File('3.file', 'Magento_Module', $parentTheme)
                            )),
                            array('*', $grandParentTheme, array(
                                new \Magento\View\Layout\File('1.file', 'Magento_Module', $grandParentTheme),
                                new \Magento\View\Layout\File('2.file', 'Magento_Module', $grandParentTheme),
                                new \Magento\View\Layout\File('3.file', 'Magento_Module', $grandParentTheme),
                            )),
                            array('1.file', $grandParentTheme, array(
                                new \Magento\View\Layout\File('1.file', 'Magento_Module', $grandParentTheme),
                                new \Magento\View\Layout\File('2.file', 'Magento_Module', $grandParentTheme),
                                new \Magento\View\Layout\File('3.file', 'Magento_Module', $grandParentTheme),
                            )),
                        );

                        foreach ($maps as $map) {
                            $result = array_pop($map);
                            if ($map[0] == $filePath && spl_object_hash($map[1]) == spl_object_hash($themeToSearch)) {
                                return $result;
                            }
                        }
                        return null;
            }));

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $model = $objectManager->create('Magento\Less\File\Source\Aggregated', array(
            'libraryFiles' => $libraryFiles,
            'baseFiles'    => $baseFiles,
            'themeFiles'   => $themeFiles
        ));

        return $model;
    }
}
