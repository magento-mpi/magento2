<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

/**
 * @magentoDataFixture Magento/Core/Model/_files/design/themes.php
 */
class MergedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Path to the public directory for view files
     *
     * @var \Magento\Filesystem\Directory\WriteInterface
     */
    protected static $_themePublicDir;

    /**
     * Path to the public directory for merged view files
     *
     * @var \Magento\Filesystem\Directory\WriteInterface
     */
    protected static $_viewPublicMergedDir;

    public static function setUpBeforeClass()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\App\Filesystem $filesystem */
        $filesystem = $objectManager->get('Magento\App\Filesystem');
        self::$_themePublicDir = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::STATIC_VIEW_DIR);
        self::$_viewPublicMergedDir = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::PUB_VIEW_CACHE_DIR);
    }

    protected function setUp()
    {
        \Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(array(
            \Magento\App\Filesystem::PARAM_APP_DIRS => array(
                \Magento\App\Filesystem::THEMES_DIR => array('path' => dirname(__DIR__) . '/_files/design'),
                \Magento\App\Filesystem::PUB_DIR => array('path' => BP),
            )
        ));
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')->setAreaCode('frontend');
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\DesignInterface')
            ->setDesignTheme('vendor_default');
    }

    protected function tearDown()
    {
        self::$_themePublicDir->delete('frontend');
        self::$_viewPublicMergedDir->delete(\Magento\View\Asset\Merged::PUBLIC_MERGE_DIR);
    }

    /**
     * Build model, containing the provided assets
     *
     * @param array $files
     * @param string $contentType
     * @return \Magento\View\Asset\Merged
     */
    protected function _buildModel(array $files, $contentType)
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $assets = array();
        foreach ($files as $file) {
            $assets[] = $objectManager->create('Magento\View\Asset\ViewFile',
                array('file' => $file, 'contentType' => $contentType));
        }

        $mergeStrategy = $objectManager->get('Magento\View\Asset\MergeStrategy\Direct');

        $model = $objectManager->create('Magento\View\Asset\Merged', array('assets' => $assets,
            'mergeStrategy' => $mergeStrategy));
        return $model;
    }

    /**
     * @param string $contentType
     * @param array $files
     * @param string $expectedFilename
     * @param array $related
     * @dataProvider getUrlDataProvider
     * @magentoConfigFixture current_store dev/css/merge_css_files 1
     * @magentoConfigFixture current_store dev/js/merge_files 1
     * @magentoConfigFixture current_store dev/static/sign 0
     */
    public function testMerging($contentType, $files, $expectedFilename, $related = array())
    {
        $resultingFile = self::$_viewPublicMergedDir->getAbsolutePath(
            \Magento\View\Asset\Merged::PUBLIC_MERGE_DIR . '/' . $expectedFilename
        );
        $this->assertFileNotExists($resultingFile);

        $model = $this->_buildModel($files, $contentType);

        $this->assertCount(1, $model);

        $model->rewind();
        $asset = $model->current();
        $mergedUrl = $asset->getUrl();
        $this->assertEquals($expectedFilename, basename($mergedUrl));

        $this->assertFileExists($resultingFile);
        foreach ($related as $file) {
            $this->assertFileExists(self::$_themePublicDir->getAbsolutePath('frontend/vendor_default/en_US/' . $file));
        }
    }

    /**
     * @param string $contentType
     * @param array $files
     * @param string $expectedFilename
     * @param array $related
     * @dataProvider getUrlDataProvider
     * @magentoConfigFixture current_store dev/css/merge_css_files 1
     * @magentoConfigFixture current_store dev/js/merge_files 1
     * @magentoConfigFixture current_store dev/static/sign 1
     */
    public function testMergingAndSigning($contentType, $files, $expectedFilename, $related = array())
    {
        $model = $this->_buildModel($files, $contentType);

        $asset = $model->current();
        $mergedUrl = $asset->getUrl();
        $mergedFileName = basename($mergedUrl);
        $mergedFileName = preg_replace('/\?.*$/i', '', $mergedFileName);
        $this->assertEquals($expectedFilename, $mergedFileName);

        foreach ($related as $file) {
            $this->assertFileExists(self::$_themePublicDir->getAbsolutePath('frontend/vendor_default/en_US/' . $file));
        }
    }

    /**
     * @return array
     */
    public static function getUrlDataProvider()
    {
        return array(
            array(
                \Magento\View\Publisher::CONTENT_TYPE_CSS,
                array(
                    'mage/calendar.css',
                    'css/file.css',
                ),
                md5(implode('|',
                        array(
                            'frontend/vendor_default/en_US/mage/calendar.css',
                            'frontend/vendor_default/en_US/css/file.css')
                    )
                ) . '.css',
                array(
                    'css/file.css',
                    'recursive.css',
                    'recursive.gif',
                    'css/deep/recursive.css',
                    'recursive2.gif',
                    'css/body.gif',
                    'css/1.gif',
                    'h1.gif',
                    'images/h2.gif',
                    'Namespace_Module/absolute_valid_module.gif',
                    'Magento_Theme/favicon.ico', // non-fixture file from real module
                ),
            ),
            array(
                \Magento\View\Publisher::CONTENT_TYPE_JS,
                array(
                    'mage/calendar.js',
                    'scripts.js',
                ),
                md5(implode('|',
                    array(
                        'frontend/vendor_default/en_US/mage/calendar.js',
                        'frontend/vendor_default/en_US/scripts.js')
                    )
                ) . '.js',
            ),
        );
    }
}
