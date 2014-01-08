<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\View;

/**
 * @magentoDataFixture Magento/Core/Model/_files/design/themes.php
 */
class DesignTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\DesignInterface
     */
    protected $_model;

    /**
     * @var \Magento\View\FileSystem
     */
    protected $_viewFileSystem;

    /**
     * @var \Magento\View\ConfigInterface
     */
    protected $_viewConfig;

    /**
     * @var \Magento\View\Url
     */
    protected $_viewUrl;

    public static function setUpBeforeClass()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Filesystem $filesystem */
        $filesystem = $objectManager->get('Magento\Filesystem');
        $themeDir = $filesystem->getDirectoryWrite(\Magento\Filesystem::MEDIA);
        $themeDir->delete('theme/frontend');
        $themeDir->delete('theme/_merged');

        $pubLibPath = $filesystem->getPath(\Magento\Filesystem::PUB_LIB);
        copy($pubLibPath . '/prototype/prototype.js', $pubLibPath . '/prototype/prototype.min.js');
    }

    public static function tearDownAfterClass()
    {
        /** @var \Magento\Filesystem $filesystem */
        $filesystem = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Filesystem');
        $pubLibPath = $filesystem->getPath(\Magento\Filesystem::PUB_LIB);
        unlink($pubLibPath . '/prototype/prototype.min.js');
    }

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_model = $objectManager->create('Magento\View\DesignInterface');
        $this->_viewFileSystem = $objectManager->create('Magento\View\FileSystem');
        $this->_viewConfig = $objectManager->create('Magento\View\ConfigInterface');
        $this->_viewUrl = $objectManager->create('Magento\View\Url');
        $objectManager->get('Magento\App\State')->setAreaCode('frontend');
    }

    /**
     * Emulate fixture design theme
     *
     * @param string $themePath
     */
    protected function _emulateFixtureTheme($themePath = 'test_default')
    {
        \Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(array(
            \Magento\Filesystem::PARAM_APP_DIRS => array(
                \Magento\Filesystem::THEMES => array('path' => realpath(__DIR__ . '/../_files/design')),
            ),
        ));
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\App')->loadArea('frontend');
        $objectManager->get('Magento\View\DesignInterface')->setDesignTheme($themePath);

        $this->_viewFileSystem = $objectManager->create('Magento\View\FileSystem');
        $this->_viewConfig = $objectManager->create('Magento\View\ConfigInterface');
        $this->_viewUrl = $objectManager->create('Magento\View\Url');
    }

    public function testSetGetArea()
    {
        $this->assertEquals(\Magento\View\DesignInterface::DEFAULT_AREA, $this->_model->getArea());
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')->setAreaCode('test');
        $this->assertEquals('test', $this->_model->getArea());
    }

    public function testSetDesignTheme()
    {
        $this->_model->setDesignTheme('test_test');
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')->setAreaCode('test');
        $this->assertEquals('test', $this->_model->getArea());
        $this->assertEquals(null, $this->_model->getDesignTheme()->getThemePath());
    }

    public function testGetDesignTheme()
    {
        $this->assertInstanceOf('Magento\View\Design\ThemeInterface', $this->_model->getDesignTheme());
    }

    /**
     * @magentoConfigFixture current_store design/theme/theme_id 0
     */
    public function testGetConfigurationDesignThemeDefaults()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $themes = array('frontend' => 'test_f', 'adminhtml' => 'test_a', 'install' => 'test_i');
        $design = $objectManager->create('Magento\Core\Model\View\Design', array('themes' => $themes));
        $objectManager->addSharedInstance($design, 'Magento\Core\Model\View\Design');

        $model = $objectManager->get('Magento\Core\Model\View\Design');

        $this->assertEquals('test_f', $model->getConfigurationDesignTheme());
        $this->assertEquals('test_f', $model->getConfigurationDesignTheme('frontend'));
        $this->assertEquals('test_f', $model->getConfigurationDesignTheme('frontend', array('store' => 0)));
        $this->assertEquals('test_f', $model->getConfigurationDesignTheme('frontend', array('store' => null)));
        $this->assertEquals('test_i', $model->getConfigurationDesignTheme('install'));
        $this->assertEquals('test_i', $model->getConfigurationDesignTheme('install', array('store' => uniqid())));
        $this->assertEquals('test_a', $model->getConfigurationDesignTheme('adminhtml'));
        $this->assertEquals('test_a', $model->getConfigurationDesignTheme('adminhtml', array('store' => uniqid())));
    }

    /**
     * @magentoConfigFixture current_store design/theme/theme_id one
     * @magentoConfigFixture fixturestore_store design/theme/theme_id two
     * @magentoDataFixture Magento/Core/_files/store.php
     */
    public function testGetConfigurationDesignThemeStore()
    {
        $storeId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\StoreManagerInterface')->getStore()->getId();
        $this->assertEquals('one', $this->_model->getConfigurationDesignTheme());
        $this->assertEquals('one', $this->_model->getConfigurationDesignTheme(null, array('store' => $storeId)));
        $this->assertEquals('one', $this->_model->getConfigurationDesignTheme('frontend', array('store' => $storeId)));
        $this->assertEquals('two', $this->_model->getConfigurationDesignTheme(null, array('store' => 'fixturestore')));
        $this->assertEquals('two', $this->_model->getConfigurationDesignTheme(
            'frontend', array('store' => 'fixturestore')
        ));
    }

    /**
     * @dataProvider getFilenameDataProvider
     * @magentoAppIsolation enabled
     */
    public function testGetFilename($file, $params)
    {
        $this->_emulateFixtureTheme();
        $this->assertFileExists($this->_viewFileSystem->getFilename($file, $params));
    }

    /**
     * @return array
     */
    public function getFilenameDataProvider()
    {
        return array(
            array('theme_file.txt', array('module' => 'Magento_Catalog')),
            array('Magento_Catalog::theme_file.txt', array()),
            array('Magento_Catalog::theme_file_with_2_dots..txt', array()),
            array('Magento_Catalog::theme_file.txt', array('module' => 'Overridden_Module')),
        );
    }

    /**
     * @param string $file
     * @expectedException \Magento\Exception
     * @dataProvider extractScopeExceptionDataProvider
     */
    public function testExtractScopeException($file)
    {
        $this->_viewFileSystem->getFilename($file, array());
    }

    public function extractScopeExceptionDataProvider()
    {
        return array(
            array('::no_scope.ext'),
            array('../file.ext'),
        );
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetViewConfig()
    {
        $this->_emulateFixtureTheme();
        $config = $this->_viewConfig->getViewConfig();
        $this->assertInstanceOf('Magento\Config\View', $config);
        $this->assertEquals(array('var1' => 'value1', 'var2' => 'value2'), $config->getVars('Namespace_Module'));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetConfigCustomized()
    {
        $this->_emulateFixtureTheme();
        /** @var $theme \Magento\View\Design\ThemeInterface */
        $theme = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\View\DesignInterface')
            ->getDesignTheme();
        $customConfigFile = $theme->getCustomization()->getCustomViewConfigPath();
        /** @var $filesystem \Magento\Filesystem */
        $filesystem = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Filesystem');
        $directory = $filesystem->getDirectoryWrite(\Magento\Filesystem::ROOT);
        $relativePath = $directory->getRelativePath($customConfigFile);
        try {
            $directory->writeFile($relativePath, '<?xml version="1.0" encoding="UTF-8"?>
                <view><vars  module="Namespace_Module"><var name="customVar">custom value</var></vars></view>');

            $config = $this->_viewConfig->getViewConfig();
            $this->assertInstanceOf('Magento\Config\View', $config);
            $this->assertEquals(array('customVar' => 'custom value'), $config->getVars('Namespace_Module'));
        } catch (\Exception $e) {
            $directory->delete($relativePath);
            throw $e;
        }
        $directory->delete($relativePath);
    }

    /**
     * @param string $appMode
     * @param string $file
     * @param string $result
     *
     * @dataProvider getViewUrlDataProvider
     *
     * @magentoConfigFixture current_store dev/static/sign 0
     * @magentoAppIsolation enabled
     */
    public function testGetViewUrl($appMode, $file, $result)
    {
        $currentAppMode = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\App\State')->getMode();
        if ($currentAppMode != $appMode) {
            $this->markTestSkipped("Implemented to be run in {$appMode} mode");
        }
        $this->_emulateFixtureTheme();
        $this->assertEquals($result, $this->_viewUrl->getViewFileUrl($file));
    }

    /**
     * @param string $appMode
     * @param string $file
     * @param string $result
     *
     * @dataProvider getViewUrlDataProvider
     *
     * @magentoConfigFixture current_store dev/static/sign 1
     * @magentoAppIsolation enabled
     */
    public function testGetViewUrlSigned($appMode, $file, $result)
    {
        $currentAppMode = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\App\State')->getMode();
        if ($currentAppMode != $appMode) {
            $this->markTestSkipped("Implemented to be run in {$appMode} mode");
        }
        $url = $this->_viewUrl->getViewFileUrl($file);
        $this->assertEquals(strpos($url, $result), 0);
        $lastModified = array();
        preg_match('/.*\?(.*)$/i', $url, $lastModified);
        $this->assertArrayHasKey(1, $lastModified);
        $this->assertEquals(10, strlen($lastModified[1]));
        $this->assertLessThanOrEqual(time(), $lastModified[1]);
        $this->assertGreaterThan(1970, date('Y', $lastModified[1]));
    }

    /**
     * @return array
     */
    public function getViewUrlDataProvider()
    {
        return array(
            array(
                \Magento\App\State::MODE_DEFAULT,
                'Magento_Theme::favicon.ico',
                'http://localhost/pub/static/frontend/test_default/en_US/Magento_Theme/favicon.ico',
            ),
            array(
                \Magento\App\State::MODE_DEFAULT,
                'prototype/prototype.js',
                'http://localhost/pub/lib/prototype/prototype.js'
            ),
            array(
                \Magento\App\State::MODE_DEVELOPER,
                'Magento_Theme::menu.js',
                'http://localhost/pub/static/frontend/test_default/en_US/Magento_Theme/menu.js'
            ),
            array(
                \Magento\App\State::MODE_DEFAULT,
                'Magento_Theme::menu.js',
                'http://localhost/pub/static/frontend/test_default/en_US/Magento_Theme/menu.js'
            ),
            array(
                \Magento\App\State::MODE_DEFAULT,
                'Magento_Catalog::widgets.css',
                'http://localhost/pub/static/frontend/test_default/en_US/Magento_Catalog/widgets.css'
            ),
            array(
                \Magento\App\State::MODE_DEVELOPER,
                'Magento_Catalog::widgets.css',
                'http://localhost/pub/static/frontend/test_default/en_US/Magento_Catalog/widgets.css'
            ),
        );
    }

    public function testGetPublicFileUrl()
    {
        $pubLibFile = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Filesystem')
                ->getPath(\Magento\Filesystem::PUB_LIB) . '/jquery/jquery.js';
        $actualResult = $this->_viewUrl->getPublicFileUrl($pubLibFile);
        $this->assertStringEndsWith('/jquery/jquery.js', $actualResult);
    }

    /**
     * @magentoConfigFixture current_store dev/static/sign 1
     */
    public function testGetPublicFileUrlSigned()
    {
        $pubLibFile = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Filesystem')
                ->getPath(\Magento\Filesystem::PUB_LIB) . '/jquery/jquery.js';
        $actualResult = $this->_viewUrl->getPublicFileUrl($pubLibFile);
        $this->assertStringMatchesFormat('%a/jquery/jquery.js?%d', $actualResult);
    }
}
