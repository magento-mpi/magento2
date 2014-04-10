<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Less\File\Collector;

class AggregatedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Less\File\Collector\Aggregated
     */
    protected $model;

    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        \Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(array(
            \Magento\App\Filesystem::PARAM_APP_DIRS => array(
                \Magento\App\Filesystem::LIB_WEB => array('path' => dirname(dirname(__DIR__)) . '/_files/lib/web'),
                \Magento\App\Filesystem::THEMES_DIR => array('path' => dirname(dirname(__DIR__)) . '/_files/design'),
            )
        ));
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->objectManager->get('Magento\App\State')->setAreaCode('frontend');

        /** @var \Magento\Filesystem $filesystem */
        $filesystem = $this->objectManager->create(
            'Magento\App\Filesystem',
            array('directoryList' => $this->objectManager->create(
                'Magento\Filesystem\DirectoryList',
                array(
                    'root' => BP,
                    'directories' => array(
                        \Magento\App\Filesystem::MODULES_DIR
                            => array('path' => dirname(dirname(__DIR__)) . '/_files/code')
                    )
                )
            ))
        );

        /** @var \Magento\View\File\Collector\Base $sourceBase */
        $sourceBase = $this->objectManager->create(
            'Magento\View\File\Collector\Base', array('filesystem' => $filesystem)
        );
        $this->model = $this->objectManager->create(
            'Magento\Less\File\Collector\Aggregated',
            array('baseFiles' => $sourceBase)
        );
    }

    /**
     * @magentoDataFixture Magento/Less/_files/themes.php
     * @magentoAppIsolation enabled
     * @magentoAppArea frontend
     * @param string $path
     * @param string $themeName
     * @param string[] $expectedFiles
     * @dataProvider getFilesDataProvider
     */
    public function testGetFiles($path, $themeName, array $expectedFiles)
    {
        /** @var \Magento\View\Design\Theme\FlyweightFactory $themeFactory */
        $themeFactory = $this->objectManager->get('Magento\View\Design\Theme\FlyweightFactory');
        $theme = $themeFactory->create($themeName);
        if (!count($expectedFiles)) {
            $this->setExpectedException('LogicException', 'magento_import returns empty result by path doesNotExist');
        }
        $files = $this->model->getFiles($theme, $path);
        $this->assertCount(count($expectedFiles), $files, 'Files number doesn\'t match');

        /** @var $file \Magento\View\File */
        foreach ($files as $file) {
            if (!in_array($file->getFilename(), $expectedFiles)) {
                $this->fail(sprintf('File "%s" is not expected but found', $file->getFilename()));
            }
        }
    }

    /**
     * @return array
     */
    public function getFilesDataProvider()
    {
        $fixtureDir = dirname(dirname(__DIR__));
        return array(
            'file in theme and parent theme' => array(
                '1.file',
                'test_default',
                array(
                    str_replace(
                        '\\',
                        '/',
                         "$fixtureDir/_files/design/frontend/test_default/web/1.file"
                    ),
                    str_replace(
                        '\\',
                        '/',
                        "$fixtureDir/_files/design/frontend/test_default/Magento_Core/web/1.file"
                    ),
                    str_replace(
                        '\\',
                        '/',
                        "$fixtureDir/_files/design/frontend/test_parent/Magento_Theme/web/1.file"
                    )
                )
            ),
            'file in library' => array(
                '2.file',
                'test_default',
                array(
                    str_replace(
                        '\\',
                        '/',
                        "$fixtureDir/_files/lib/web/2.file"
                    )
                )
            ),
            'non-existing file' => array(
                'doesNotExist',
                'test_default',
                array()
            ),
            'file in library, module, and theme' => array(
                '3.less',
                'test_default',
                array(
                    str_replace(
                        '\\',
                        '/',
                        "$fixtureDir/_files/lib/web/3.less"
                    ),
                    str_replace(
                        '\\',
                        '/',
                        "$fixtureDir/_files/code/Magento/Theme/view/frontend/web/3.less"
                    ),
                    str_replace(
                        '\\',
                        '/',
                        "$fixtureDir/_files/design/frontend/test_default/Magento_Core/web/3.less"
                    )
                )
            ),
        );
    }
}
