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
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        \Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(array(
            \Magento\App\Filesystem::PARAM_APP_DIRS => array(
                \Magento\App\Filesystem::LIB_WEB => array('path' => dirname(dirname(__DIR__)) . '/_files/lib'),
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

        /** @var \Magento\Less\File\Source\Base $sourceBase */
        $sourceBase = $this->objectManager->create('Magento\Less\File\Source\Base', array('filesystem' => $filesystem));
        $this->model = $this->objectManager->create(
            'Magento\Less\File\Source\Aggregated',
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
    public function testGetFiles($path, $themeName, $expectedFiles)
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
                        dirname(dirname(__DIR__)) . '/_files/code/Magento/Other/view/frontend/3.less'
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
}
