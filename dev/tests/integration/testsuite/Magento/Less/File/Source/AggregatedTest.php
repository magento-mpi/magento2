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
                \Magento\Filesystem::THEMES => array('path' => dirname(dirname(__DIR__)) . '/_files/design'),
                \Magento\Filesystem::MODULES => array('path' => dirname(dirname(__DIR__)) . '/_files/code')
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
