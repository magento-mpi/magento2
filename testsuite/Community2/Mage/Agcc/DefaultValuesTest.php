<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Agcc
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Auto Generated Specific Coupon Codes checking default values in Coupons Information form (SCPR)
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_Agcc_DefaultValuesTest extends Mage_Selenium_TestCase
{
    public function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('customers_promotions');
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-3900
     * @author yaroslav.goncharuk
     */
    public function codeLengthDefaultValue()
    {
        //Steps
        $this->fillField('code_length', '10');
        $this->clickButton('save_config');
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadDataSet('Agcc', 'scpr_required_fields_with_agcc');
        $this->agccHelper()->createRuleAndContinueEdit($ruleData);
        $this->openTab('manage_coupon_codes');
        //Verification
        if (!$this->isElementPresent('//input[@id="coupons_length" and @value="10"]')) {
            $this->fail('Wrong specified default value in Code Length field');
        }
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-3934
     * @author yaroslav.goncharuk
     */
    public function codeFormatDefaultValue()
    {
        //Steps
        $this->fillDropdown('code_format', 'Numeric');
        $this->clickButton('save_config');
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadDataSet('Agcc', 'scpr_required_fields_with_agcc');
        $this->agccHelper()->createRuleAndContinueEdit($ruleData);
        $this->openTab('manage_coupon_codes');
        //Verification
        if (!$this->isElementPresent('//select[@id="coupons_format"]//option[@selected="selected" and @value="num"]')) {
            $this->fail('Wrong specified default value in Code Format dropdown');
        }
    }

    /**
     * @test
     * @param string $value
     * @dataProvider codePrefixDefaultValueDataProvider
     * @TestlinkId TL-MAGE-3935
     * @author yaroslav.goncharuk
     */
    public function codePrefixDefaultValue($value)
    {
        //Steps
        $this->fillField('code_prefix', $value);
        $this->clickButton('save_config');
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadDataSet('Agcc', 'scpr_required_fields_with_agcc');
        $this->agccHelper()->createRuleAndContinueEdit($ruleData);
        $this->openTab('manage_coupon_codes');
        $xpath = "//input[@id='coupons_prefix' and @value='" . $value . "']";
        //Verification
        if (!$this->isElementPresent($xpath)) {
            $this->fail('Wrong specified default value in Code Prefix field');
        }
    }

    public function codePrefixDefaultValueDataProvider()
    {
        return array(
            array($this->generate('string', 5, ':alnum:')),
            array($this->generate('string', 5, ':alpha:')),
            array($this->generate('string', 5, ':digit:')),
            array($this->generate('string', 5, ':lower:')),
            array($this->generate('string', 5, ':upper:')),
            array($this->generate('string', 5, ':punct:')),
        );
    }

    /**
     * @test
     * @param string $value
     * @dataProvider codeSuffixDefaultValueDataProvider
     * @TestlinkId TL-MAGE-3936
     * @author yaroslav.goncharuk
     */
    public function codeSuffixDefaultValue($value)
    {
        //Steps
        $this->fillField('code_suffix', $value);
        $this->clickButton('save_config');
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadDataSet('Agcc', 'scpr_required_fields_with_agcc');
        $this->agccHelper()->createRuleAndContinueEdit($ruleData);
        $this->openTab('manage_coupon_codes');
        $xpath = "//input[@id='coupons_suffix' and @value='". $value ."']";
        //Verification
        if (!$this->isElementPresent($xpath)) {
            $this->fail('Wrong specified default value in Code Suffix field');
        }
    }

    public function codeSuffixDefaultValueDataProvider()
    {
        return array(
            array($this->generate('string', 5, ':alnum:')),
            array($this->generate('string', 5, ':alpha:')),
            array($this->generate('string', 5, ':digit:')),
            array($this->generate('string', 5, ':lower:')),
            array($this->generate('string', 5, ':upper:')),
            array($this->generate('string', 5, ':punct:')),
        );
    }

    /**
     * @test
     * @TestlinkId TL-MAGE-3937
     * @author yaroslav.goncharuk
     */
    public function dashDefaultValue()
    {
        //Steps
        $this->fillField('dash_every_x_characters', '3');
        $this->clickButton('save_config');
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadDataSet('Agcc', 'scpr_required_fields_with_agcc');
        $this->agccHelper()->createRuleAndContinueEdit($ruleData);
        $this->openTab('manage_coupon_codes');
        //Verification
        if (!$this->isElementPresent('//input[@id="coupons_dash" and @value="3"]')) {
            $this->fail('Wrong specified default value in Dash Every X Characters field');
        }
    }
}