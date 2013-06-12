<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Captcha
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Captcha ConfigValidation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Captcha_ConfigValidationTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Captcha/default_frontend_captcha');
    }

    /**
     * <p>Wrong value "Number of Symbols"</p>
     *
     * @param string $value
     * @dataProvider wrongNumberOfSymbolsDataProvider
     * @test
     * @TestlinkId TL-MAGE-5783
     */
    public function wrongNumberOfSymbols($value)
    {
        $config = $this->loadDataSet('Captcha', 'front_captcha_after_attempts_to_login',
            array('captcha_word_length' => $value));
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError',
            '"Number of Symbols": The value is not within the specified range.');
        $this->systemConfigurationHelper()->configure($config);
    }

    public function wrongNumberOfSymbolsDataProvider()
    {
        return array(
            array('q'),
            array('9'),
            array('0'),
            array('2-10'),
            array('2.1'));
    }

    /**
     * <p>Correct value "Number of Symbols"</p>
     *
     * @param string $value
     * @dataProvider correctNumberOfSymbolsDataProvider
     * @test
     * @TestlinkId TL-MAGE-5784
     */
    public function correctNumberOfSymbols($value)
    {
        $config = $this->loadDataSet('Captcha', 'front_captcha_after_attempts_to_login',
            array('captcha_word_length' => $value));
        $this->systemConfigurationHelper()->configure($config);
    }

    public function correctNumberOfSymbolsDataProvider()
    {
        return array(
            array('1'),
            array('8'),
            array('2-5'));
    }
}