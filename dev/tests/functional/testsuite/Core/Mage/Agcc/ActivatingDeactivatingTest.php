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
 * * Auto Generated Specific Coupon Codes functionality activating and deactivating in shopping cart price rules
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Agcc_ActivatingDeactivatingTest extends Mage_Selenium_TestCase
{
    public function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_shopping_cart_price_rules');
    }

    /**
     * <p>Creating SCPR without activating Auto Generated Specific Coupon Codes functionality</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3757
     */
    public function createWithoutAgcc()
    {
        //Data
        $ruleData = $this->loadDataSet('Agcc', 'scpr_required_fields_without_agcc');
        $verificationData = $this->loadDataSet('Agcc', 'verification_scpr_required_fields_without_agcc');
        //Steps
        $this->priceRulesHelper()->createRuleAndContinueEdit($ruleData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->priceRulesHelper()->verifyRuleData($verificationData);
        //Steps
        $this->openTab('manage_coupon_codes');
        //Verification
        $this->assertFalse($this->controlIsEditable('field', 'coupon_qty'), 'Manage Coupon Codes tab is active');
    }

    /**
     * <p>Creating SCPR with activating Auto Generated Specific Coupon Codes functionality</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3757
     */
    public function createWithAgcc()
    {
        //Data
        $ruleData = $this->loadDataSet('Agcc', 'scpr_required_fields_with_agcc');
        //Steps
        $this->priceRulesHelper()->createRuleAndContinueEdit($ruleData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->priceRulesHelper()->verifyRuleData($ruleData);
        //Steps
        $this->openTab('manage_coupon_codes');
        //Verification
        $this->assertTrue($this->controlIsEditable('field', 'coupon_qty'), 'Manage Coupon Codes tab is not active');
    }

    /**
     * <p>Creating SCPR with activating Auto Generated Specific Coupon Codes functionality and specified coupon code
     *
     * @test
     * @TestlinkId TL-MAGE-3757
     */
    public function createWithAgccAndCouponCode()
    {
        //Data
        $ruleData = $this->loadDataSet('Agcc', 'scpr_required_fields_with_agcc_and_coupon_code');
        $verificationData = $this->loadDataSet('Agcc', 'verification_scpr_required_fields_with_agcc_and_coupon_code');
        //Steps
        $this->priceRulesHelper()->createRuleAndContinueEdit($ruleData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->priceRulesHelper()->verifyRuleData($verificationData);
        //Steps
        $this->openTab('manage_coupon_codes');
        //Verification
        $this->assertTrue($this->controlIsEditable('field', 'coupon_qty'), 'Manage Coupon Codes tab is not active');
    }

    /**
     * <p>Creating SCPR with activating Auto Generated Specific Coupon Codes functionality negative test</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3760
     */
    public function createWithAgccNegative()
    {
        //Data
        $ruleData = $this->loadDataSet('Agcc', 'scpr_required_fields_with_agcc_negative');
        //Steps
        $this->priceRulesHelper()->createRule($ruleData);
        //Verification
        $this->addFieldIdToMessage('field', 'coupon_code');
        $this->assertMessagePresent('validation', 'empty_required_field');
    }

    /**
     * <p>Deactivating Auto Generated Specific Coupon Codes functionality in SCPR</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3761
     */
    public function deactivateManageCouponCodesTab()
    {
        //Data
        $ruleData = $this->loadDataSet('Agcc', 'scpr_required_fields_with_agcc');
        //Steps
        $this->priceRulesHelper()->createRuleAndContinueEdit($ruleData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->priceRulesHelper()->verifyRuleData($ruleData);
        //Steps
        $this->openTab('manage_coupon_codes');
        //Verification
        $this->assertTrue($this->controlIsEditable('field', 'coupon_qty'), 'Manage Coupon Codes tab is not active');
        //Data
        $ruleData = $this->loadDataSet('Agcc', 'scpr_required_fields_deactivate_agcc');
        //Steps
        $this->priceRulesHelper()->fillTabs($ruleData);
        $this->addParameter('elementTitle', $ruleData['info']['rule_name']);
        $this->saveAndContinueEdit('button', 'save_and_continue_edit');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_rule');
        //Steps
        $this->openTab('manage_coupon_codes');
        //Verification
        $this->assertFalse($this->controlIsEditable('field', 'coupon_qty'), 'Manage Coupon Codes tab is active');
    }
}