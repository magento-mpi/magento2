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
 * Tests for the view layer fallback mechanism
 */
class Mage_Core_Model_Design_PackageFallbackTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Design_Package
     */
    protected $_model;

    public static function setUpBeforeClass()
    {
        Mage::app()->getConfig()->getOptions()->setDesignDir(
            dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'design'
        );
    }

    protected function setUp()
    {
        $this->_model = new Mage_Core_Model_Design_Package();
        $this->_model->setDesignTheme('test/default/default', 'frontend')
            ->setIsFallbackSavePermitted(false);
    }

    public function testGetFilename()
    {
        $expected = '%s/frontend/test/default/Mage_Catalog/theme_template.phtml';
        $actual = $this->_model->getFilename('Mage_Catalog::theme_template.phtml', array());
        $this->_testExpectedVersusActualFilename($expected, $actual);
    }

    public function testGetLocaleFileName()
    {
        $expected = '%s/frontend/test/default/locale/en_US/translate.csv';
        $actual = $this->_model->getLocaleFileName('translate.csv', array());
        $this->_testExpectedVersusActualFilename($expected, $actual);
    }

    public function testGetSkinFile()
    {
        $expected = '%s/frontend/package/custom_theme/skin/theme_nested_skin/Fixture_Module/fixture_script.js';
        $params = array(
            'package' => 'package',
            'theme' => 'custom_theme',
            'skin' => 'theme_nested_skin'
        );
        $actual = $this->_model->getSkinFile('Fixture_Module::fixture_script.js', $params);
        $this->_testExpectedVersusActualFilename($expected, $actual);
    }

    /**
     * Tests expected vs actual found fallback filename
     *
     * @param string $expected
     * @param string $actual
     */
    protected function _testExpectedVersusActualFilename($expected, $actual)
    {
        $expected = str_replace('/', DIRECTORY_SEPARATOR, $expected);
        $this->assertStringMatchesFormat($expected, $actual);
        $this->assertFileExists($actual);
    }

    /**
     * @covers Mage_Core_Model_Design_PackageFallbackTest::setIsFallbackSavePermitted
     */
    public function testOnShutdown()
    {
        if (Mage::getIsDeveloperMode()) {
            $this->markTestSkipped('Valid in non-developer mode only');
        }

        $mapsDir = Mage::getConfig()->getTempVarDir() . '/maps/fallback';
        $exception = null;
        try {
            $this->assertEmpty(glob($mapsDir . '/*.*'));

            $this->_model->getLocaleFileName('translate.csv', array());

            $this->_model->onShutdown();
            $this->assertEmpty(glob($mapsDir . '/*.*'));

            $this->_model->setIsFallbackSavePermitted(true)
                ->onShutdown();
            $this->assertNotEmpty(glob($mapsDir . '/*.*'));
        } catch (Exception $exception) {
        }

        $this->_model->setIsFallbackSavePermitted(false);
        Varien_Io_File::rmdirRecursive($mapsDir);

        if ($exception) {
            throw $exception;
        }
    }
}
