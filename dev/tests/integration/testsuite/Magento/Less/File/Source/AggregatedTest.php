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

    /**
     * @magentoDataFixture Magento/Less/_files/themes.php
     * @magentoAppIsolation enabled
     * @magentoAppArea frontend
     * @param string $path
     * @param string $themeName
     * @param string[] $expectedFiles
     * @dataProvider getFilesDataProvider
     */
    public function testGetFiles($path, $themeName, $expectedFiles)
    {
        \Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(array(
            \Magento\Filesystem::PARAM_APP_DIRS => array(
                \Magento\Filesystem::PUB_LIB => array('path' => dirname(dirname(__DIR__)) . '/_files/lib'),
                \Magento\Filesystem::THEMES => array('path' => dirname(dirname(__DIR__)) . '/_files/design')
            )
        ));

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\App\State')->setAreaCode('frontend');

        $this->model = $objectManager->create('Magento\Less\File\Source\Aggregated');

        /** @var \Magento\View\Design\Theme\FlyweightFactory $themeFactory */
        $themeFactory = $objectManager->get('Magento\View\Design\Theme\FlyweightFactory');
        $theme = $themeFactory->create($themeName);
        if (!count($expectedFiles)) {
            $this->setExpectedException('LogicException', 'magento_import returns empty result by path doesNotExist');
        }
        $files = $this->model->getFiles($theme, $path);
        $this->assertEquals(count($expectedFiles), count($files), 'Files number doesn\'t match');

        /** @var $file \Magento\View\Layout\File */
        foreach ($files as $file) {
            if (!in_array($file->getFilename(), $expectedFiles)) {
                $this->fail(sprintf('File "%s" is not expected but found', $file->getFilename()));
            }
        }
    }

    public function getFilesDataProvider()
    {
        return array(
            array(
                '1.file',
                'test_default',
                array(
                    str_replace(
                        '\\',
                        '/',
                        dirname(dirname(__DIR__)) . '/_files/design/frontend/test_default/1.file'
                    ),
                    str_replace(
                        '\\',
                        '/',
                        dirname(dirname(__DIR__)) . '/_files/design/frontend/test_default/Magento_Module/1.file'
                    ),
                    str_replace(
                        '\\',
                        '/',
                        dirname(dirname(__DIR__)) . '/_files/design/frontend/test_parent/Magento_Second/1.file'
                    )
                )
            ),
            array(
                '2.file',
                'test_default',
                array(
                    str_replace(
                        '\\',
                        '/',
                        dirname(dirname(__DIR__)) . '/_files/lib/2.file'
                    )
                )
            ),
            array(
                'doesNotExist',
                'test_default',
                array()
            ),
            array(
                '3',
                'test_default',
                array(
                    str_replace(
                        '\\',
                        '/',
                        dirname(dirname(__DIR__)) . '/_files/lib/3.less'
                    ),
                    str_replace(
                        '\\',
                        '/',
                        dirname(dirname(__DIR__)) . '/_files/design/frontend/test_default/Magento_Third/3.less'
                    )
                )
            ),
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
