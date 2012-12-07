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
 *
 * @magentoAppIsolation enabled
 * @magentoDataFixture Mage/Core/Model/_files/design/test_default_theme.php
 */
class Mage_Core_Model_Design_PackageFallbackTest extends PHPUnit_Framework_TestCase
{
    public function testGetFilename()
    {
        $model = Mage::getDesign();

        $expected = '%s/frontend/test/default/Mage_Catalog/theme_template.phtml';
        $actual = $model->getFilename('Mage_Catalog::theme_template.phtml', array());
        $this->_testExpectedVersusActualFilename($expected, $actual);
    }

    public function testGetLocaleFileName()
    {
        $model = Mage::getDesign();

        $expected = '%s/frontend/test/default/locale/en_US/translate.csv';
        $actual = $model->getLocaleFileName('translate.csv', array());
        $this->_testExpectedVersusActualFilename($expected, $actual);
    }

    public function testGetViewFile()
    {
        $model = Mage::getDesign();

        $expected = '%s/frontend/package/custom_theme/Fixture_Module/fixture_script.js';
        $params = array(
            'package' => 'package',
            'theme' => 'custom_theme'
        );
        $actual = $model->getViewFile('Fixture_Module::fixture_script.js', $params);
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
}
