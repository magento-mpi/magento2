<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDbIsolation enabled
 */
class Mage_Core_Model_Design_PackageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Design_Package
     */
    protected $_model;

    protected static $_developerMode;

    public static function setUpBeforeClass()
    {
        Varien_Io_File::rmdirRecursive(Mage::app()->getConfig()->getOptions()->getMediaDir() . '/theme');

        $ioAdapter = new Varien_Io_File();
        $ioAdapter->cp(
            Mage::app()->getConfig()->getOptions()->getJsDir() . '/prototype/prototype.js',
            Mage::app()->getConfig()->getOptions()->getJsDir() . '/prototype/prototype.min.js'
        );
        self::$_developerMode = Mage::getIsDeveloperMode();
    }

    public static function tearDownAfterClass()
    {
        $ioAdapter = new Varien_Io_File();
        $ioAdapter->rm(Mage::app()->getConfig()->getOptions()->getJsDir() . '/prototype/prototype.min.js');
        Mage::setIsDeveloperMode(self::$_developerMode);
    }

    protected function setUp()
    {
        /** @var $themeUtility Mage_Core_Utility_Theme */
        $themeUtility = Mage::getModel('Mage_Core_Utility_Theme', array(
            dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'design',
            Mage::getModel('Mage_Core_Model_Design_Package')
        ));
        $themeUtility->registerThemes()->setDesignTheme('test/default', 'frontend');
        $this->_model = $themeUtility->getDesign();
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testSetGetArea()
    {
        $this->assertEquals(Mage_Core_Model_Design_Package::DEFAULT_AREA, $this->_model->getArea());
        $this->_model->setArea('test');
        $this->assertEquals('test', $this->_model->getArea());
    }

    public function testGetTheme()
    {
        $this->assertEquals('test/default', $this->_model->getDesignTheme()->getThemePath());
    }

    public function testSetDesignTheme()
    {
        $this->_model->setDesignTheme('test/test', 'test');
        $this->assertEquals('test', $this->_model->getArea());
        $this->assertEquals(null, $this->_model->getDesignTheme()->getThemePath());
    }

    public function testGetDesignTheme()
    {
        $this->assertInstanceOf('Mage_Core_Model_Theme', $this->_model->getDesignTheme());
    }

    /**
     * @dataProvider getFilenameDataProvider
     */
    public function testGetFilename($file, $params)
    {
        $this->assertFileExists($this->_model->getFilename($file, $params));
    }

    /**
     * @return array
     */
    public function getFilenameDataProvider()
    {
        return array(
            array('theme_file.txt', array('module' => 'Mage_Catalog')),
            array('Mage_Catalog::theme_file.txt', array()),
            array('Mage_Catalog::theme_file_with_2_dots..txt', array()),
            array('Mage_Catalog::theme_file.txt', array('module' => 'Overriden_Module')),
        );
    }

    /**
     * @param string $file
     * @expectedException Magento_Exception
     * @dataProvider extractScopeExceptionDataProvider
     */
    public function testExtractScopeException($file)
    {
        $this->_model->getFilename($file, array());
    }

    public function extractScopeExceptionDataProvider()
    {
        return array(
            array('::no_scope.ext'),
            array('./file.ext'),
            array('../file.ext'),
            array('dir/./file.ext'),
            array('dir/../file.ext'),
        );
    }

    public function testGetOptimalCssUrls()
    {
        $expected = array(
            'http://localhost/pub/media/theme/frontend/test/default/en_US/css/styles.css',
            'http://localhost/pub/lib/mage/translate-inline.css',
        );
        $params = array(
            'css/styles.css',
            'mage/translate-inline.css',
        );
        $this->assertEquals($expected, $this->_model->getOptimalCssUrls($params));
    }

    /**
     * @param array $files
     * @param array $expectedFiles
     * @dataProvider getOptimalCssUrlsMergedDataProvider
     * @magentoConfigFixture current_store dev/css/merge_css_files 1
     */
    public function testGetOptimalCssUrlsMerged($files, $expectedFiles)
    {
        $this->assertEquals($expectedFiles, $this->_model->getOptimalCssUrls($files));
    }

    public function getOptimalCssUrlsMergedDataProvider()
    {
        return array(
            array(
                array('css/styles.css', 'mage/calendar.css'),
                array('http://localhost/pub/media/theme/_merged/dce6f2a22049cd09bbfbe344fc73b037.css')
            ),
            array(
                array('css/styles.css'),
                array('http://localhost/pub/media/theme/frontend/test/default/en_US/css/styles.css',)
            ),
        );
    }


    public function testGetOptimalJsUrls()
    {
        $expected = array(
            'http://localhost/pub/media/theme/frontend/test/default/en_US/js/tabs.js',
            'http://localhost/pub/lib/jquery/jquery-ui-timepicker-addon.js',
            'http://localhost/pub/lib/mage/calendar.js',
        );
        $params = array(
            'js/tabs.js',
            'jquery/jquery-ui-timepicker-addon.js',
            'mage/calendar.js',
        );
        $this->assertEquals($expected, $this->_model->getOptimalJsUrls($params));
    }

    /**
     * @param array $files
     * @param array $expectedFiles
     * @dataProvider getOptimalJsUrlsMergedDataProvider
     * @magentoConfigFixture current_store dev/js/merge_files 1
     */
    public function testGetOptimalJsUrlsMerged($files, $expectedFiles)
    {
        $this->assertEquals($expectedFiles, $this->_model->getOptimalJsUrls($files));
    }

    public function getOptimalJsUrlsMergedDataProvider()
    {
        return array(
            array(
                array('js/tabs.js', 'mage/calendar.js', 'jquery/jquery-ui-timepicker-addon.js'),
                array('http://localhost/pub/media/theme/_merged/51cf03344697f37c2511aa0ad3391d56.js',)
            ),
            array(
                array('mage/calendar.js'),
                array('http://localhost/pub/lib/mage/calendar.js',)
            ),
        );
    }

    public function testGetViewConfig()
    {
        $config = $this->_model->getViewConfig();
        $this->assertInstanceOf('Magento_Config_View', $config);
        $this->assertEquals(array('var1' => 'value1', 'var2' => 'value2'), $config->getVars('Namespace_Module'));
    }

    /**
     * @param string $file
     * @param string $result
     * @covers Mage_Core_Model_Design_Package::getViewUrl
     * @dataProvider getViewUrlDataProvider
     * @magentoConfigFixture current_store dev/static/sign 0
     */
    public function testGetViewUrl($devMode, $file, $result)
    {
        Mage::setIsDeveloperMode($devMode);
        $this->assertEquals($this->_model->getViewFileUrl($file), $result);
    }

    /**
     * @param string $file
     * @param string $result
     * @covers Mage_Core_Model_Design_Package::getSkinUrl
     * @dataProvider getViewUrlDataProvider
     * @magentoConfigFixture current_store dev/static/sign 1
     */
    public function testGetViewUrlSigned($devMode, $file, $result)
    {
        Mage::setIsDeveloperMode($devMode);
        $url = $this->_model->getViewFileUrl($file);
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
                false,
                'Mage_Page::favicon.ico',
                'http://localhost/pub/media/theme/frontend/test/default/en_US/Mage_Page/favicon.ico',
            ),
            array(
                true,
                'prototype/prototype.js',
                'http://localhost/pub/lib/prototype/prototype.js'
            ),
            array(
                false,
                'prototype/prototype.js',
                'http://localhost/pub/lib/prototype/prototype.min.js'
            ),
            array(
                true,
                'Mage_Page::menu.js',
                'http://localhost/pub/media/theme/frontend/test/default/en_US/Mage_Page/menu.js'
            ),
            array(
                false,
                'Mage_Page::menu.js',
                'http://localhost/pub/media/theme/frontend/test/default/en_US/Mage_Page/menu.js'
            ),
            array(
                false,
                'Mage_Catalog::widgets.css',
                'http://localhost/pub/media/theme/frontend/test/default/en_US/Mage_Catalog/widgets.css'
            ),
            array(
                true,
                'Mage_Catalog::widgets.css',
                'http://localhost/pub/media/theme/frontend/test/default/en_US/Mage_Catalog/widgets.css'
            ),
        );
    }
}
