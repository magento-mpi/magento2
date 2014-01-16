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
        /** @var \Magento\Filesystem $filesystem */
        $filesystem = $objectManager->get('Magento\Filesystem');
        self::$_themePublicDir = $filesystem->getDirectoryWrite(\Magento\Filesystem::STATIC_VIEW);
        self::$_viewPublicMergedDir = $filesystem->getDirectoryWrite(\Magento\Filesystem::PUB_VIEW_CACHE);
    }

    protected function setUp()
    {
        \Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(array(
            \Magento\Filesystem::PARAM_APP_DIRS => array(
                \Magento\Filesystem::THEMES => array('path' => dirname(dirname(__DIR__)) . '/_files/design'),
                \Magento\Filesystem::PUB => array('path' => BP),
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
        $assets = array();
        foreach ($files as $file) {
            $assets[] = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\View\Asset\ViewFile',
                array('file' => $file, 'contentType' => $contentType));
        }
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\View\Asset\Merged', array('assets' => $assets));
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
        $this->markTestSkipped('Task: MAGETWO-18162');
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
        $this->markTestSkipped('Task: MAGETWO-18162');
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
                'e6ae894165d22b7d57a0f5644b6ef160.css',
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
                'e81061cbad0d8b6fe328225d0df7dace.js',
            ),
        );
    }
}
