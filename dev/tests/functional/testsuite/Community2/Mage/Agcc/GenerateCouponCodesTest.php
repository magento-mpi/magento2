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
 * Auto Generated Specific Coupon Codes configuring Coupons Information form and generating coupon codes
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_Agcc_GenerateCouponCodesTest extends Mage_Selenium_TestCase
{
    public function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadDataSet('Agcc', 'scpr_required_fields_with_agcc');
        $this->agccHelper()->createRuleAndContinueEdit($ruleData);
        $this->openTab('manage_coupon_codes');
    }

    /**
     * <p>Generating coupon codes with blank "Coupon Qty" field</p>
     * <p>Steps:</p>
     * <p>1. Leave field "Coupon Qty" blank</p>
     * <p>2. Press button "Generate"</p>
     * <p>Expected result:</p>
     * <p>Received the validation message "This is a required field." under "Coupon Qty" field</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3763
     * @author yaroslav.goncharuk
     */
    public function withBlankCouponQtyField()
    {
        //Steps
        $this->fillField('coupon_qty', '');
        $this->clickButton('generate', false);
        //Verification
        $this->assertMessagePresent('validation', 'validation_for_field_coupon_qty');
    }

    /**
     * <p>Generating coupon codes with filled "Coupon Qty" field</p>
     * <p>Steps:</p>
     * <p>1. Enter any integer value in "Coupon Qty" field</p>
     * <p>2. Press button "Generate"</p>
     * <p>Expected result:</p>
     * <p>Received the message "10 Coupon(s) have been generated" on page</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3762
     * @author yaroslav.goncharuk
     */
    public function withFilledCouponQtyField()
    {
        //Steps
        $this->fillField('coupon_qty', '10');
        $this->addParameter('qtyGeneratedCoupons', '10');
        $this->clickButton('generate', false);
        $this->pleaseWait();
        //Verification
        $this->assertMessagePresent('success', 'success_generated_coupons');
    }

    /**
     * <p>Generating coupon codes with filled zero in "Coupon Qty" field</p>
     * <p>Steps:</p>
     * <p>1. Enter zero in "Coupon Qty" field</p>
     * <p>2. Press button "Generate"</p>
     * <p>Expected result:</p>
     * <p>Received the message "Please enter a number greater than 0 in this field." under "Coupon Qty" field</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3766
     * @author yaroslav.goncharuk
     */
    public function withFilledCouponQtyFieldZero()
    {
        //Steps
        $this->fillField('coupon_qty', '0');
        $this->clickButton('generate', false);
        $this->pleaseWait();
        //Verification
        $this->assertMessagePresent('validation', 'validation_for_field_coupon_qty_zero');
    }

    /**
     * <pGenerating coupon codes with wrong values in "Coupon Qty" field</p>
     * <p>Steps:</p>
     * <p>1. Enter a decimal numeric value/negative numeric value/not numeric value/any special characters in the "Coupon Qty" field</p>
     * <p>2. Press button "Generate"</p>
     * <p>Expected result:</p>
     * <p>Received the validation message "Please use numbers only in this field. Please avoid spaces or other characters such as dots or commas." under "Coupon Qty" field</p>
     *
     * @test
     * @param string $value
     * @dataProvider withFilledCouponQtyFieldNegativeDataProvider
     * @TestlinkId TL-MAGE-3763
     * @author yaroslav.goncharuk
     */
    public function withFilledCouponQtyFieldNegative($value)
    {
        //Steps
        $this->fillField('coupon_qty', $value);
        $this->clickButton('generate', false);
        //Verification
        $this->assertMessagePresent('validation', 'validation_for_field_coupon_qty_negative');
    }

    public function withFilledCouponQtyFieldNegativeDataProvider()
    {
        return array(
            array('5.1'),
            array('-5'),
            array('text'),
            array('/'),
        );
    }

    /**
     * <p>Generating coupon codes with blank "Code Length" field</p>
     * <p>Steps:</p>
     * <p>1. Leave field "Code Length" blank</p>
     * <p>2. Press button "Generate"</p>
     * <p>Expected result:</p>
     * <p>Received the validation message "This is a required field." under "Code Length" field</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3766
     * @author yaroslav.goncharuk
     */
    public function withBlankCodeLengthField()
    {
        //Steps
        $this->fillField('code_length', '');
        $this->clickButton('generate', false);
        //Verification
        $this->assertMessagePresent('validation', 'validation_for_field_code_length');
    }

    /**
     * <p>Generating coupon codes with filled "Code Length" field</p>
     * <p>Steps:</p>
     * <p>1. Enter any integer value in "Code Length" field</p>
     * <p>2. Press button "Generate"</p>
     * <p>Expected result:</p>
     * <p>Received the message "10 Coupon(s) have been generated" on page</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3765
     * @author yaroslav.goncharuk
     */
    public function withFilledCodeLengthField()
    {
        //Steps
        $this->fillField('coupon_qty', '10');
        $this->fillField('code_length', '10');
        $this->addParameter('qtyGeneratedCoupons', '10');
        $this->clickButton('generate', false);
        $this->pleaseWait();
        //Verification
        $this->assertMessagePresent('success', 'success_generated_coupons');
    }

    /**
     * <pGenerating coupon codes with wrong values in "Code Length" field</p>
     * <p>Steps:</p>
     * <p>1. Enter a decimal numeric value/negative numeric value/not numeric value/any special characters in the "Code Length" field</p>
     * <p>2. Press button "Generate"</p>
     * <p>Expected result:</p>
     * <p>Received the validation message "Please use numbers only in this field. Please avoid spaces or other characters such as dots or commas." under "Code Length" field</p>
     *
     * @test
     * @dataProvider withFilledCouponQtyFieldNegativeDataProvider
     * @param string $value
     * @TestlinkId TL-MAGE-3766
     * @author yaroslav.goncharuk
     */
    public function withFilledCodeLengthFieldNegative($value)
    {
        //Steps
        $this->fillField('code_length', $value);
        $this->clickButton('generate', false);
        //Verification
        $this->assertMessagePresent('validation', 'validation_for_field_code_length_negative');
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
     * <p>Generating coupon codes with filled "Code Format" field</p>
     * <p>Steps:</p>
     * <p>1. Select any integer value in "Code Format" dropdown</p>
     * <p>2. Press button "Generate"</p>
     * <p>Expected result:</p>
     * <p>Received the message "10 Coupon(s) have been generated" on page</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3866
     * @author yaroslav.goncharuk
     */
    public function withFilledCodeFormatField()
    {
        //Steps
        $this->fillField('coupon_qty', '10');
        $this->fillField('code_length', '10');
        $this->fillDropdown('code_format', 'Numeric');
        $this->addParameter('qtyGeneratedCoupons', '10');
        $this->clickButton('generate', false);
        $this->pleaseWait();
        //Verification
        $this->assertMessagePresent('success', 'success_generated_coupons');
    }

    /**
     * <p>Generating coupon codes with any values in "Code Prefix" field</p>
     * <p>Steps:</p>
     * <p>1. Enter any value that contains characters, number, special characters and spaces in the "Code Prefix" field</p>
     * <p>2. Press button "Generate"</p>
     * <p>Expected result:</p>
     * <p>Received the message "n Coupon(s) have been generated" on page</p>
     *
     * @test
     * @dataProvider withFilledCodeSuffixFieldDataProvider
     * @param string $value
     * @TestlinkId TL-MAGE-3867
     * @author yaroslav.goncharuk
     */
    public function withFilledCodePrefixField($value)
    {
        //Steps
        $this->fillField('coupon_qty', '10');
        $this->fillField('code_length', '10');
        $this->fillField('code_prefix', $value);
        $this->addParameter('qtyGeneratedCoupons', '10');
        $this->clickButton('generate', false);
        $this->pleaseWait();
        //Verification
        $this->assertMessagePresent('success', 'success_generated_coupons');
    }

    /**
     * <p>Generating coupon codes with any values in "Code Suffix" field</p>
     * <p>Steps:</p>
     * <p>1. Enter any value that contains characters, number, special characters and spaces in the "Code Suffix" field</p>
     * <p>2. Press button "Generate"</p>
     * <p>Expected result:</p>
     * <p>Received the message "n Coupon(s) have been generated" on page</p>
     *
     * @test
     * @dataProvider withFilledCodeSuffixFieldDataProvider
     * @param string $value
     * @TestlinkId TL-MAGE-3869
     * @author yaroslav.goncharuk
     */
    public function withFilledCodeSuffixField($value)
    {
        //Steps
        $this->fillField('coupon_qty', '10');
        $this->fillField('code_length', '10');
        $this->fillField('code_suffix', $value);
        $this->addParameter('qtyGeneratedCoupons', '10');
        $this->clickButton('generate', false);
        $this->pleaseWait();
        //Verification
        $this->assertMessagePresent('success', 'success_generated_coupons');
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
     * <p>Generating coupon codes with blank "Dash Every X Characters" field</p>
     * <p>Steps:</p>
     * <p>1. Leave field "Dash Every X Characters" blank</p>
     * <p>2. Press button "Generate"</p>
     * <p>Expected result:</p>
     * <p>Received the message "n Coupon(s) have been generated" on page</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3880
     * @author yaroslav.goncharuk
     */
    public function withBlankDashField()
    {
        //Steps
        $this->fillField('coupon_qty', '10');
        $this->fillField('code_length', '10');
        $this->fillField('dash_every_x_characters', '');
        $this->addParameter('qtyGeneratedCoupons', '10');
        $this->clickButton('generate', false);
        $this->pleaseWait();
        //Verification
        $this->assertMessagePresent('success', 'success_generated_coupons');
    }

    /**
     * <p>Generating coupon codes with filled "Dash Every X Characters" field</p>
     * <p>Steps:</p>
     * <p>1. Enter any integer value in "Dash Every X Characters" field</p>
     * <p>2. Press button "Generate"</p>
     * <p>Expected result:</p>
     * <p>Received the message "n Coupon(s) have been generated" on page</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3870
     * @author yaroslav.goncharuk
     */
    public function withFilledDashField()
    {
        //Steps
        $this->fillField('coupon_qty', '10');
        $this->fillField('code_length', '10');
        $this->fillField('dash_every_x_characters', '2');
        $this->addParameter('qtyGeneratedCoupons', '10');
        $this->clickButton('generate', false);
        $this->pleaseWait();
        //Verification
        $this->assertMessagePresent('success', 'success_generated_coupons');
    }

    /**
     * <pGenerating coupon codes with wrong values in "Dash Every X Characters" field</p>
     * <p>Steps:</p>
     * <p>1. Enter a decimal numeric value/negative numeric value/not numeric value/any special characters in the "Dash Every X Characters" field</p>
     * <p>2. Press button "Generate"</p>
     * <p>Expected result:</p>
     * <p>Received the validation message "Please use numbers only in this field. Please avoid spaces or other characters such as dots or commas." under "Dash Every X Characters" field</p>
     *
     * @test
     * @dataProvider withFilledDashFieldNegativeDataProvider
     * @param string $value
     * @TestlinkId TL-MAGE-3873
     * @author yaroslav.goncharuk
     */
    public function withFilledDashFieldNegative($value)
    {
        //Steps
        $this->fillField('coupon_qty', '10');
        $this->fillField('code_length', '10');
        $this->fillField('dash_every_x_characters', $value);
        $this->clickButton('generate', false);
        //Verification
        $this->assertMessagePresent('validation', 'validation_for_field_dash_every_x_characters_negative');
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