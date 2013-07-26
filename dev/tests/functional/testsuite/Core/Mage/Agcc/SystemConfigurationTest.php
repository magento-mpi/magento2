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
 * * Auto Generated Specific Coupon Codes configuring default values in system configuration
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Agcc_SystemConfigurationTest extends Mage_Selenium_TestCase
{
    public function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('customers_promotions');
        $this->systemConfigurationHelper()->expandFieldSet('auto_generated_specific_coupon_codes');
    }

    /**
     * <p>Saving system settings for Auto Generated Specific Coupon Codes with blank Code Length field</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3747
     */
    public function withBlankCodeLengthField()
    {
        //Steps
        $this->fillField('code_length', '');
        $this->clickButton('save_config');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_config');
    }

    /**
     * <p>Saving system settings for Auto Generated Specific Coupon Codes with filled Code Length field</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3747
     */
    public function withFilledCodeLengthField()
    {
        //Steps
        $this->fillField('code_length', '100');
        $this->clickButton('save_config');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_config');
    }

    /**
     * <p>Saving system settings for Auto Generated Specific Coupon Codes with wrong values in Code Length field</p>
     *
     * @test
     * @param string $value
     * @dataProvider withFilledCodeLengthFieldNegativeDataProvider
     * @TestlinkId TL-MAGE-3748
     */
    public function withFilledCodeLengthFieldNegative($value)
    {
        //Steps
        $this->fillField('code_length', $value);
        $this->clickButton('save_config', false);
        //Verification
        $this->addFieldIdToMessage('field', 'code_length');
        $this->assertMessagePresent('validation', 'enter_valid_number');
    }

    public function withFilledCodeLengthFieldNegativeDataProvider()
    {
        return array(
            array('5.1'),
            array('-5'),
            array('text'),
            array('/'),
        );
    }

    /**
     * <p>Saving system settings for Auto Generated Specific Coupon Codes with changed Code format dropdown</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3749
     */
    public function withFilledCodeFormatField()
    {
        //Steps
        $this->fillDropdown('code_format', 'Numeric');
        $this->clickButton('save_config');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_config');
    }

    /**
     * <p>Saving system settings for Auto Generated Specific Coupon Codes with any values in Code Prefix field</p>
     *
     * @test
     * @param string $value
     * @dataProvider withFilledCodePrefixFieldDataProvider
     * @TestlinkId TL-MAGE-3751
     */
    public function withFilledCodePrefixField($value)
    {
        //Steps
        $this->fillField('code_prefix', $value);
        $this->clickButton('save_config');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_config');
    }

    public function withFilledCodePrefixFieldDataProvider()
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
     * <p>Saving system settings for Auto Generated Specific Coupon Codes with any values in Code Suffix field</p>
     *
     * @test
     * @param string $value
     * @dataProvider withFilledCodeSuffixFieldDataProvider
     * @TestlinkId TL-MAGE-3753
     */
    public function withFilledCodeSuffixField($value)
    {
        //Steps
        $this->fillField('code_suffix', $value);
        $this->clickButton('save_config');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_config');
    }

    public function withFilledCodeSuffixFieldDataProvider()
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
     * <p>Saving system settings for Auto Generated Specific Coupon Codes with filled Dash Every X Characters field</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3755
     */
    public function withFilledDashField()
    {
        //Steps
        $this->fillField('dash_every_x_characters', '2');
        $this->clickButton('save_config');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_config');
    }

    /**
     * <p>Saving system settings for Auto Generated Specific Coupon Codes with blank Dash Every X Characters field</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3755
     */
    public function withBlankDashField()
    {
        //Steps
        $this->fillField('dash_every_x_characters', '');
        $this->clickButton('save_config');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_config');
    }

    /**
     * <p>Saving system settings for Auto Generated Specific Coupon Codes</p>
     * <p>with wrong values in Dash Every X Characters field</p>
     *
     * @test
     * @param string $value
     * @dataProvider withFilledDashFieldNegativeDataProvider
     * @TestlinkId TL-MAGE-3756
     */
    public function withFilledDashFieldNegative($value)
    {
        //Steps
        $this->fillField('dash_every_x_characters', $value);
        $this->clickButton('save_config', false);
        //Verification
        $this->addFieldIdToMessage('field', 'dash_every_x_characters');
        $this->assertMessagePresent('validation', 'enter_valid_number');
    }

    public function withFilledDashFieldNegativeDataProvider()
    {
        return array(
            array('5.1'),
            array('-5'),
            array('text'),
            array('/'),
        );
    }
}