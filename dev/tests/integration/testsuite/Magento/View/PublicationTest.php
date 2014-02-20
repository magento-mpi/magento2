<?php
/**
 * {license_notice}
 *
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

use Magento\TestFramework\Helper\Bootstrap;

class PublicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\DesignInterface
     */
    protected $model;

    /**
     * @var \Magento\View\Service
     */
    protected $viewService;

    /**
     * @var \Magento\View\FileSystem
     */
    protected $fileSystem;

    /**
     * @var \Magento\View\Url
     */
    protected $viewUrl;

    /**
     * @var \Magento\View\FileResolver
     */
    protected $fileResolver;

    protected function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get('Magento\App\State')->setAreaCode('frontend');
        $this->viewService = $objectManager->create('Magento\View\Service');
        $this->fileSystem = $objectManager->create('Magento\View\FileSystem');
        $this->fileResolver = $objectManager->create('Magento\View\FileResolver');
        $this->model = $objectManager->get('Magento\View\DesignInterface');
    }

    protected function tearDown()
    {
        /** @var \Magento\App\Filesystem $filesystem */
        $filesystem = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\App\Filesystem');
        $publicDir = $filesystem->getDirectoryWrite(\Magento\App\Filesystem::STATIC_VIEW_DIR);
        $publicDir->delete('adminhtml');
        $publicDir->delete('frontend');
        $this->model = null;
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetPublicDir()
    {
        /** @var \Magento\App\Filesystem $filesystem */
        $filesystem = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\Filesystem');
        $expectedPublicDir = $filesystem->getPath(\Magento\App\Filesystem::STATIC_VIEW_DIR);
        $this->assertEquals($expectedPublicDir, $this->viewService->getPublicDir());
    }

    /**
     * @magentoDataFixture Magento/Core/Model/_files/design/themes.php
     * @magentoAppIsolation enabled
     * @dataProvider getViewUrlFilesDuplicationDataProvider
     */
    public function testGetViewUrlFilesDuplication($file, $expectedUrl, $locale = null)
    {
        $this->_initTestTheme();

        Bootstrap::getObjectManager()->get('Magento\Core\Model\LocaleInterface')->setLocale($locale);
        /** @var \Magento\View\Url $urlModel */
        $urlModel = Bootstrap::getObjectManager()->create('Magento\View\Url');
        $this->assertStringEndsWith($expectedUrl, $urlModel->getViewFileUrl($file));
        $viewFile = $this->fileSystem->getViewFile($file);
        $this->assertFileExists($viewFile);
    }

    /**
     * @return array
     */
    public function getViewUrlFilesDuplicationDataProvider()
    {
        return array(
            'theme file' => array(
                'css/styles.css',
                'static/frontend/test_default/en_US/css/styles.css',
            ),
            'theme localized file' => array(
                'logo.gif',
                'static/frontend/test_default/fr_FR/logo.gif',
                'fr_FR',
            ),
            'modular file' => array(
                'Namespace_Module::favicon.ico',
                'static/frontend/test_default/en_US/Namespace_Module/favicon.ico',
            ),
            'lib folder' => array(
                'varien',
                'static/frontend/test_default/en_US/varien',
            )
        );
    }

    /**
     * @expectedException \Magento\Exception
     * @dataProvider getPublicViewFileExceptionDataProvider
     */
    public function testGetPublicViewFileException($file)
    {
        $this->fileResolver->getPublicViewFile($file);
    }

    /**
     * @return array
     */
    public function getPublicViewFileExceptionDataProvider()
    {
        return array(
            'non-existing theme file'  => array('path/to/non-existing-file.ext'),
            'non-existing module file' => array('Some_Module::path/to/non-existing-file.ext'),
        );
    }

    /**
     * Test on vulnerability for protected files
     *
     * @magentoDataFixture Magento/Core/Model/_files/design/themes.php
     * @magentoAppIsolation enabled
     * @dataProvider getProtectedFiles
     * @param array $designParams
     * @param string $filePath
     * @param string $expectedExceptionMsg
     */
    public function testTemplatePublicationVulnerability($designParams, $filePath, $expectedExceptionMsg)
    {
        $this->_initTestTheme();
        $this->setExpectedException('Magento\Exception', $expectedExceptionMsg);
        $this->fileResolver->getPublicViewFile($filePath, $designParams);
    }

    /**
     * Return files, which are not published
     *
     * @return array
     */
    public function getProtectedFiles()
    {
        return array(
            'theme PHP file' => array(
                array('area' => 'frontend', 'theme' => 'vendor_default'),
                'malicious_file.php',
                "Files with extension 'php' may not be published"
            ),
            'theme XML file' => array(
                array('area' => 'frontend', 'theme' => 'vendor_default'),
                'malicious_file.xml',
                "Files with extension 'xml' may not be published"
            ),
            'modular XML file' => array(
                array('area' => 'frontend', 'theme' => 'test_default', 'module' => 'Magento_Catalog'),
                'malicious_file.xml',
                "Files with extension 'xml' may not be published"
            ),
            'modular PHTML file' => array(
                array('area' => 'frontend', 'theme' => 'test_default', 'module' => 'Magento_Core'),
                'malicious_file.phtml',
                "Files with extension 'phtml' may not be published"
            ),
        );
    }


    /**
     * Publication of view files in development mode
     *
     * @param string $file
     * @param $designParams
     * @param string $expectedFile
     * @magentoDataFixture Magento/Core/Model/_files/design/themes.php
     * @magentoAppIsolation enabled
     * @dataProvider getPublicViewFileDataProvider
     */
    public function testGetPublicViewFile($file, $designParams, $expectedFile)
    {
        $this->_initTestTheme();

        $expectedFile = $this->viewService->getPublicDir() . '/' . $expectedFile;

        // test doesn't make sense if the original file doesn't exist or the target file already exists
        $originalFile = $this->fileSystem->getViewFile($file, $designParams);
        $this->assertFileExists($originalFile);

        // trigger publication
        $this->assertFileNotExists($expectedFile, 'Please verify isolation from previous test(s).');
        $this->fileResolver->getPublicViewFile($file, $designParams);
        $this->assertFileExists($expectedFile);

        // as soon as the files are published, they must have the same mtime as originals
        $this->assertEquals(
            filemtime($originalFile),
            filemtime($expectedFile),
            "These files mtime must be equal: {$originalFile} / {$expectedFile}"
        );
    }

    /**
     * @return array
     */
    public function getPublicViewFileDataProvider()
    {
        $designParams = array(
            'area'    => 'frontend',
            'theme'   => 'test_default',
            'locale'  => 'en_US'
        );
        return array(
            'library file' => array(
                'jquery/jquery-ui.js',
                $designParams,
                'frontend/test_default/en_US/jquery/jquery-ui.js',
            ),
            'view file' => array(
                'images/logo_email.gif',
                $designParams,
                'frontend/test_default/en_US/images/logo_email.gif',
            ),
            'view modular file' => array(
                'Magento_Theme::favicon.ico',
                $designParams,
                'frontend/test_default/en_US/Magento_Theme/favicon.ico',
            ),
        );
    }

    /**
     * @param string $file
     * @param array $designParams
     * @param string $expectedFile
     * @param string $contentFile
     * @magentoDataFixture Magento/Core/Model/_files/design/themes.php
     * @magentoAppIsolation enabled
     * @dataProvider getPublicFileLessFormatDataProvider
     */
    public function testGetPublicFileLessFormat($file, $designParams, $expectedFile, $contentFile)
    {
        $this->_initTestTheme();

        $expectedFile = $this->viewService->getPublicDir() . '/' . $expectedFile;

        // test doesn't make sense if the original file doesn't exist or the target file already exists
        $originalFile = $this->fileSystem->getViewFile($file, $designParams);
        $this->assertFileNotExists($originalFile);

        // trigger publication
        $this->assertFileNotExists($expectedFile, 'Please verify isolation from previous test(s).');
        $this->fileResolver->getPublicViewFile($file, $designParams);
        $this->assertFileExists($expectedFile);

        $this->assertEquals(
            trim(file_get_contents($this->fileSystem->getViewFile($contentFile, $designParams))),
            file_get_contents($expectedFile)
        );
    }

    public function getPublicFileLessFormatDataProvider()
    {
        $designParams = array(
            'area'    => 'frontend',
            'theme'   => 'test_default',
            'locale'  => 'en_US'
        );
        return array(
            'view file' => array(
                'source.css', // source.less is supposed to be found by this request
                $designParams,
                'frontend/test_default/en_US/source.css', // and then .less file will be converted into css
                'result_source.css'
            )
        );
    }

    /**
     * Publication of CSS files located in the theme (development mode)
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Core/Model/_files/design/themes.php
     */
    public function testPublishCssFileFromTheme()
    {
        $this->_initTestTheme();
        $expectedFiles = array(
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
        );
        $publishedDir = $this->viewService->getPublicDir() . '/frontend/vendor_default/en_US';
        $this->assertFileNotExists($publishedDir, 'Please verify isolation from previous test(s).');
        $this->fileResolver->getPublicViewFile('css/file.css', array(
            'theme'   => 'vendor_default',
            'locale'  => 'en_US'
        ));
        foreach ($expectedFiles as $file) {
            $this->assertFileExists("{$publishedDir}/{$file}");
        }
        $this->assertFileNotExists("{$publishedDir}/absolute.gif");
    }

    /**
     * Publication of CSS files located in the module
     *
     * @magentoDataFixture Magento/Core/Model/_files/design/themes.php
     * @dataProvider publishCssFileFromModuleDataProvider
     */
    public function testPublishCssFileFromModule(
        $cssViewFile, $designParams, $expectedCssFile, $expectedCssContent, $expectedRelatedFiles
    ) {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')
            ->loadArea(\Magento\Core\Model\App\Area::AREA_FRONTEND);
        $this->fileResolver->getPublicViewFile($cssViewFile, $designParams);

        $expectedCssFile = $this->viewService->getPublicDir() . '/' . $expectedCssFile;
        $this->assertFileExists($expectedCssFile);
        $actualCssContent = file_get_contents($expectedCssFile);

        $this->assertNotRegExp(
            '/url\(.*?' . preg_quote(\Magento\View\Service::SCOPE_SEPARATOR, '/') . '.*?\)/',
            $actualCssContent,
            'Published CSS file must not contain scope separators in URLs.'
        );

        foreach ($expectedCssContent as $expectedCssSubstring) {
            $this->assertContains($expectedCssSubstring, $actualCssContent);
        }

        foreach ($expectedRelatedFiles as $expectedFile) {
            $expectedFile = $this->viewService->getPublicDir() . '/' . $expectedFile;
            $this->assertFileExists($expectedFile);
        }
    }

    /**
     * @return array
     */
    public function publishCssFileFromModuleDataProvider()
    {
        return array(
            'frontend' => array(
                'product/product.css',
                array(
                    'area'    => 'adminhtml',
                    'theme'   => 'magento_backend',
                    'locale'  => 'en_US',
                    'module'  => 'Magento_Catalog',
                ),
                'adminhtml/magento_backend/en_US/Magento_Catalog/product/product.css',
                array(
                    'url(../../Magento_Backend/images/gallery-image-base-label.png)',
                ),
                array(
                    'adminhtml/magento_backend/en_US/Magento_Backend/images/gallery-image-base-label.png',
                ),
            ),
            'adminhtml' => array(
                'Magento_Paypal::styles.css',
                array(
                    'area'    => 'adminhtml',
                    'theme'   => 'vendor_test',
                    'locale'  => 'en_US',
                    'module'  => false,
                ),
                'adminhtml/vendor_test/en_US/Magento_Paypal/styles.css',
                array(
                    'url(images/paypal-logo.png)',
                    'url(images/pp-allinone.png)',
                ),
                array(
                    'adminhtml/vendor_test/en_US/Magento_Paypal/images/paypal-logo.png',
                    'adminhtml/vendor_test/en_US/Magento_Paypal/images/pp-allinone.png',
                ),
            ),
        );
    }

    /**
     * Test that modified CSS file and changed resources are re-published in developer mode
     *
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Core/_files/media_for_change.php
     */
    public function testPublishResourcesAndCssWhenChangedCssDevMode()
    {
        $mode = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')
            ->getMode();
        if ($mode != \Magento\App\State::MODE_DEVELOPER) {
            $this->markTestSkipped('Valid in developer mode only');
        }
        $this->_testPublishResourcesAndCssWhenChangedCss(true);
    }

    /**
     * Test that modified CSS file and changed resources are not re-published in usual mode
     *
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Core/_files/media_for_change.php
     */
    public function testNotPublishResourcesAndCssWhenChangedCssUsualMode()
    {
        $mode = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')
            ->getMode();
        if ($mode == \Magento\App\State::MODE_DEVELOPER) {
            $this->markTestSkipped('Valid in non-developer mode only');
        }
        $this->_testPublishResourcesAndCssWhenChangedCss(false);
    }

    /**
     * Tests what happens when CSS file and its resources are changed - whether they are re-published or not
     *
     * @param bool $expectedPublished
     */
    protected function _testPublishResourcesAndCssWhenChangedCss($expectedPublished)
    {
        $appInstallDir = \Magento\TestFramework\Helper\Bootstrap::getInstance()->getAppInstallDir();

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\App\State')->setAreaCode('frontend');

        $model = $objectManager->get('Magento\View\DesignInterface');
        $model->setDesignTheme('test_default');

        $viewService = $objectManager->create('Magento\View\Service');
        $fileResolver = $objectManager->create('Magento\View\FileResolver');

        $themePath = $model->getDesignTheme()->getFullPath();

        $fixtureViewPath = "$appInstallDir/media_for_change/$themePath/";
        $publishedPath = $viewService->getPublicDir() . "/$themePath/en_US/";

        $fileResolver->getPublicViewFile('style.css', array('locale' => 'en_US'));

        //It's added to make 'mtime' really different for source and origin files
        sleep(1);

        // Change main file and referenced files - everything changed and referenced must appear
        file_put_contents(
            $fixtureViewPath . 'style.css',
            'div {background: url(images/rectangle.gif);}',
            FILE_APPEND
        );
        file_put_contents(
            $fixtureViewPath . 'sub.css',
            '.sub2 {border: 1px solid magenta}',
            FILE_APPEND
        );
        $fileResolver->getPublicViewFile('style.css', array('locale' => 'en_US'));

        $assertFileComparison = $expectedPublished ? 'assertFileEquals' : 'assertFileNotEquals';
        $this->$assertFileComparison($fixtureViewPath . 'style.css', $publishedPath . 'style.css');
        $this->$assertFileComparison($fixtureViewPath . 'sub.css', $publishedPath . 'sub.css');
        if ($expectedPublished) {
            $this->assertFileEquals(
                $fixtureViewPath . 'images/rectangle.gif', $publishedPath . 'images/rectangle.gif'
            );
        } else {
            $this->assertFileNotExists($publishedPath . 'images/rectangle.gif');
        }
    }

    /**
     * Test changed resources, referenced in non-modified CSS file, are re-published
     *
     * @magentoDataFixture Magento/Core/_files/media_for_change.php
     * @magentoAppIsolation enabled
     */
    public function testPublishChangedResourcesWhenUnchangedCssDevMode()
    {
        $mode = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')->getMode();
        if ($mode != \Magento\App\State::MODE_DEVELOPER) {
            $this->markTestSkipped('Valid in developer mode only');
        }

        $this->_testPublishChangedResourcesWhenUnchangedCss(true);
    }

    /**
     * Test changed resources, referenced in non-modified CSS file, are re-published
     *
     * @magentoDataFixture Magento/Core/_files/media_for_change.php
     * @magentoAppIsolation enabled
     */
    public function testNotPublishChangedResourcesWhenUnchangedCssUsualMode()
    {
        $mode = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')
            ->getMode();
        if ($mode == \Magento\App\State::MODE_DEVELOPER) {
            $this->markTestSkipped('Valid in non-developer mode only');
        }

        $this->_testPublishChangedResourcesWhenUnchangedCss(false);
    }

    /**
     * Tests what happens when CSS file and its resources are changed - whether they are re-published or not
     *
     * @param bool $expectedPublished
     */
    protected function _testPublishChangedResourcesWhenUnchangedCss($expectedPublished)
    {
        $appInstallDir = \Magento\TestFramework\Helper\Bootstrap::getInstance()->getAppInstallDir();
        \Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(array(
            \Magento\App\Filesystem::PARAM_APP_DIRS => array(
                \Magento\App\Filesystem::THEMES_DIR => array('path' => "$appInstallDir/media_for_change"),
            )
        ));

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\App\State')->setAreaCode('frontend');

        $model = $objectManager->get('Magento\View\DesignInterface');
        $model->setDesignTheme('test_default');

        $viewService = $objectManager->create('Magento\View\Service');
        $fileResolver = $objectManager->create('Magento\View\FileResolver');

        $themePath = $model->getDesignTheme()->getFullPath();
        $fixtureViewPath = "$appInstallDir/media_for_change/$themePath/";
        $publishedPath = $viewService->getPublicDir() . "/$themePath/en_US/";

        $fileResolver->getPublicViewFile('style.css', array('locale' => 'en_US'));

        //It's added to make 'mtime' really different for source and origin files
        sleep(1);

        // Change referenced files
        copy($fixtureViewPath . 'images/rectangle.gif', $fixtureViewPath . 'images/square.gif');
        touch($fixtureViewPath . 'images/square.gif');
        file_put_contents(
            $fixtureViewPath . 'sub.css',
            '.sub2 {border: 1px solid magenta}',
            FILE_APPEND
        );

        $fileResolver->getPublicViewFile('style.css', array('locale' => 'en_US'));

        $assertFileComparison = $expectedPublished ? 'assertFileEquals' : 'assertFileNotEquals';
        $this->$assertFileComparison($fixtureViewPath . 'sub.css', $publishedPath . 'sub.css');
        $this->$assertFileComparison($fixtureViewPath . 'images/rectangle.gif', $publishedPath . 'images/square.gif');
    }

    /**
     * Init the model with a test theme from fixture themes dir
     * Init application with custom view dir, @magentoAppIsolation required
     */
    protected function _initTestTheme()
    {
        \Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(array(
            \Magento\App\Filesystem::PARAM_APP_DIRS => array(
                \Magento\App\Filesystem::THEMES_DIR => array('path' => dirname(__DIR__) . '/Core/Model/_files/design')
            )
        ));
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\App\State')->setAreaCode('frontend');

        // Reinit model with new directories
        $this->model = $objectManager->get('Magento\View\DesignInterface');
        $this->model->setDesignTheme('test_default');

        $this->viewService = $objectManager->create('Magento\View\Service');
        $this->fileSystem = $objectManager->create('Magento\View\FileSystem');
    }

    /**
     * Check that the mechanism of publication not affected data content on css files
     *
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testCssWithBase64Data()
    {
        \Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(array(
            \Magento\App\Filesystem::PARAM_APP_DIRS => array(
                \Magento\App\Filesystem::THEMES_DIR => array('path' => dirname(__DIR__) . '/Core/Model/_files/design/')
            )
        ));
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')->loadAreaPart(
            \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
            \Magento\Core\Model\App\Area::PART_CONFIG
        );

        /** @var $themeCollection \Magento\Core\Model\Theme\Collection */
        $themeCollection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Theme\Collection');
        $theme = $themeCollection
            ->addTargetPattern('frontend/vendor_default/theme.xml')
            ->getFirstItem()
            ->save();

        $publishedPath = $this->viewService->getPublicDir() . '/frontend/vendor_default/en_US';
        $params =  array(
            'area'    => 'frontend',
            'theme'   => 'vendor_default',
            'locale'  => 'en_US',
            'themeModel' => $theme
        );
        $filePath = $this->fileSystem->getViewFile('css/base64.css', $params);

        // publish static content
        $this->fileResolver->getPublicViewFile('css/base64.css', $params);
        $this->assertFileEquals($filePath, "{$publishedPath}/css/base64.css");

        $this->model->setDesignTheme(\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\View\Design\ThemeInterface'));
    }

    /**
     * Publication of view files in development mode
     *
     * @param string $file
     * @param $designParams
     * @param string $expectedFile
     * @magentoDataFixture Magento/Core/Model/_files/design/themes.php
     * @magentoAppIsolation enabled
     * @dataProvider getPublicViewFileDataProvider
     * @see testGetPublicViewFile
     */
    public function testGetPublicViewFile2($file, $designParams, $expectedFile)
    {
        $this->_initTestTheme();

        $expectedFile = $this->viewService->getPublicDir() . '/' . $expectedFile;

        $this->assertFileNotExists($expectedFile, 'Please verify isolation from previous test(s).');
        $this->fileResolver->getPublicViewFile($file, $designParams);
        $this->assertFileExists($expectedFile);
    }

    public function testGetPublicViewFileExistingFile()
    {
        $filePath = 'mage/mage.js';
        $expectedFile = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\Filesystem')
                ->getPath(\Magento\App\Filesystem::LIB_WEB) . '/' . $filePath;
        $this->assertFileExists($expectedFile, 'Please verify existence of the library file ' . $filePath);

        $actualFile = $this->fileResolver->getPublicViewFile($filePath);
        $this->assertFileEquals($expectedFile, $actualFile);
    }
}
