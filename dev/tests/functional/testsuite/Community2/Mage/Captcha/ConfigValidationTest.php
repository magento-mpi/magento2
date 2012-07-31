<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Captcha ConfigValidation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Community2_Mage_Captcha_ConfigValidationTest extends Mage_Selenium_TestCase
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
        $this->systemConfigurationHelper()->configure($this->loadDataSet('Captcha', 'disable_frontend_captcha'));
    }

    /**
     * <p>Wrong value "Number of Symbols"</p>
     * <p>Steps:</p>
     * <p>1.Open CAPTCHA tab in System-Configuration->Customer Configuration</p>
     * <p>2.Input wrong value in "Number of Symbols" field </p>
     * <p>3.Try Save config</p>
     * <p>Expected result</p>
     * <p>"The value is not within the specified range." message is show below fied</p>
     *
     * @param string $value
     * @dataProvider wrongNumberOfSymbolsDataProvider
     * @test
     * @TestlinkId TL-MAGE-5783
     */
    public function wrongNumberOfSymbols($value)
    {
        $config = $this->loadDataSet('Captcha', 'front_captcha_after_attempts_to_login',
                                     array ('captcha_word_length' => $value));
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
     * <p>Steps:</p>
     * <p>1.Open CAPTCHA tab in System-Configuration->Customer Configuration</p>
     * <p>2.Input correct value in "Number of Symbols" field </p>
     * <p>3. Save config</p>
     * <p>Expected result</p>
     * <p>Configuration successfully saved</p>
     *
     * @param string $value
     * @dataProvider correctNumberOfSymbolsDataProvider
     * @test
     * @TestlinkId TL-MAGE-5784
     */
    public function correctNumberOfSymbols($value)
    {
        $config = $this->loadDataSet('Captcha', 'front_captcha_after_attempts_to_login',
                                     array ('captcha_word_length' => $value));
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
