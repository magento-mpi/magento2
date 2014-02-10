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
class Core_Mage_Agcc_GenerateCouponCodesTest extends Mage_Selenium_TestCase
{
    public function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadDataSet('Agcc', 'scpr_required_fields_with_agcc');
        $this->priceRulesHelper()->createRuleAndContinueEdit($ruleData);
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->openTab('manage_coupon_codes');
    }

    /**
     * <p>Generating coupon codes with blank "Coupon Qty" field</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3763
     */
    public function withBlankCouponQtyField()
    {
        //Steps
        $this->fillField('coupon_qty', '');
        $this->clickButton('generate', false);
        //Verification
        $this->addFieldIdToMessage('field', 'coupon_qty');
        $this->assertMessagePresent('validation', 'empty_required_field');
    }

    /**
     * <p>Generating coupon codes with filled "Coupon Qty" field</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3762
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
     *
     * @test
     * @TestlinkId TL-MAGE-3766
     */
    public function withFilledCouponQtyFieldZero()
    {
        //Steps
        $this->fillField('coupon_qty', '0');
        $this->clickButton('generate', false);
        $this->pleaseWait();
        //Verification
        $this->addFieldIdToMessage('field', 'coupon_qty');
        $this->assertMessagePresent('validation', 'enter_greater_than_zero');
    }

    /**
     * <p>Generating coupon codes with wrong values in "Coupon Qty" field</p>
     *
     * @test
     * @param string $value
     * @dataProvider withFilledCouponQtyFieldNegativeDataProvider
     * @TestlinkId TL-MAGE-3763
     */
    public function withFilledCouponQtyFieldNegative($value)
    {
        //Steps
        $this->fillField('coupon_qty', $value);
        $this->clickButton('generate', false);
        //Verification
        $this->addFieldIdToMessage('field', 'coupon_qty');
        $this->assertMessagePresent('validation', 'use_numbers_only');
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
     *
     * @test
     * @TestlinkId TL-MAGE-3766
     */
    public function withBlankCodeLengthField()
    {
        //Steps
        $this->fillField('coupon_qty', 2);
        $this->fillField('code_length', '');
        $this->clickButton('generate', false);
        //Verification
        $this->addFieldIdToMessage('field', 'code_length');
        $this->assertMessagePresent('validation', 'empty_required_field');
    }

    /**
     * <p>Generating coupon codes with filled "Code Length" field</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3765
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
     * <p>Generating coupon codes with wrong values in "Code Length" field</p>
     *
     * @test
     * @dataProvider withFilledCouponQtyFieldNegativeDataProvider
     * @param string $value
     * @TestlinkId TL-MAGE-3766
     */
    public function withFilledCodeLengthFieldNegative($value)
    {
        //Steps
        $this->fillField('coupon_qty', 2);
        $this->fillField('code_length', $value);
        $this->clickButton('generate', false);
        //Verification
        $this->addFieldIdToMessage('field', 'code_length');
        $this->assertMessagePresent('validation', 'use_numbers_only');
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
     *
     * @test
     * @TestlinkId TL-MAGE-3866
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
     *
     * @test
     * @dataProvider withFilledCodeSuffixFieldDataProvider
     * @param string $value
     * @TestlinkId TL-MAGE-3867
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
     *
     * @test
     * @dataProvider withFilledCodeSuffixFieldDataProvider
     * @param string $value
     * @TestlinkId TL-MAGE-3869
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
     *
     * @test
     * @TestlinkId TL-MAGE-3880
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
     *
     * @test
     * @TestlinkId TL-MAGE-3870
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
     * <p>Generating coupon codes with wrong values in "Dash Every X Characters" field</p>
     *
     * @test
     * @dataProvider withFilledDashFieldNegativeDataProvider
     * @param string $value
     * @TestlinkId TL-MAGE-3873
     */
    public function withFilledDashFieldNegative($value)
    {
        //Steps
        $this->fillField('coupon_qty', '10');
        $this->fillField('code_length', '10');
        $this->fillField('dash_every_x_characters', $value);
        $this->clickButton('generate', false);
        //Verification
        $this->addFieldIdToMessage('field', 'dash_every_x_characters');
        $this->assertMessagePresent('validation', 'use_numbers_only');
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