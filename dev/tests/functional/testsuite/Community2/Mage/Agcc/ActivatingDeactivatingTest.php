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
class Community2_Mage_Agcc_ActivatingDeactivatingTest extends Mage_Selenium_TestCase
{
    public function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_shopping_cart_price_rules');
    }

    /**
     * <p>Creating SCPR without activating Auto Generated Specific Coupon Codes functionality</p>
     * <p>Steps:</p>
     * <p>1. Press button "Add New Rule"</p>
     * <p>2. Fill all required fields</p>
     * <p>3. Fill any value to "Coupon Code" field</p>
     * <p>4. Set "Use Auto Generation" checkbox to "No"</p>
     * <p>5. Press button "Save and Continue Edit"</p>
     * <p>Expected result:</p>
     * <p>Received the message that The rule has been saved.</p>
     * <p>Checkbox "Use Auto Generation" remains set to "No"</p>
     * <p>"Manage Coupon Codes" tab appears</p>
     * <p>"Manage Coupon Codes" tab is inactive</p>
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
        $this->agccHelper()->createRuleAndContinueEdit($ruleData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->verifyForm($verificationData['info'], 'rule_information');
        //Steps
        $this->openTab('manage_coupon_codes');
        $fieldXpath = $this->_getControlXpath('field', 'coupon_qty');
        //Verification
        if ($this->isEditable($fieldXpath)) {
            $this->fail('Manage Coupon Codes tab is active');
        }
    }

    /**
     * <p>Creating SCPR with activating Auto Generated Specific Coupon Codes functionality</p>
     * <p>Steps:</p>
     * <p>1. Press button "Add New Rule"</p>
     * <p>2. Fill all required fields</p>
     * <p>3. Set "Use Auto Generation" checkbox to "Yes"</p>
     * <p>4. Press button "Save and Continue Edit"</p>
     * <p>Expected result:</p>
     * <p>Received the message that The rule has been saved.</p>
     * <p>Checkbox "Use Auto Generation" remains set to "Yes"</p>
     * <p>"Manage Coupon Codes" tab appears</p>
     * <p>"Manage Coupon Codes" tab is active</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3757
     */
    public function createWithAgcc()
    {
        //Data
        $ruleData = $this->loadDataSet('Agcc', 'scpr_required_fields_with_agcc');
        //Steps
        $this->agccHelper()->createRuleAndContinueEdit($ruleData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->verifyForm($ruleData['info'], 'rule_information');
        //Steps
        $this->openTab('manage_coupon_codes');
        $fieldXpath = $this->_getControlXpath('field', 'coupon_qty');
        //Verification
        if (!$this->isEditable($fieldXpath)) {
            $this->fail('Manage Coupon Codes tab is not active');
        }
    }

    /**
     * <p>Creating SCPR with activating Auto Generated Specific Coupon Codes functionality and specified coupon code
     * in "Coupon Code" field</p>
     * <p>Steps:</p>
     * <p>1. Press button "Add New Rule"</p>
     * <p>2. Fill all required fields</p>
     * <p>3. Fill any value to "Coupon Code" field</p>
     * <p>4. Set "Use Auto Generation" checkbox to "Yes"</p>
     * <p>5. Press button "Save and Continue Edit"</p>
     * <p>Expected result:</p>
     * <p>Received the message that The rule has been saved.</p>
     * <p>Checkbox "Use Auto Generation" remains set to "Yes"</p>
     * <p>"Manage Coupon Codes" tab appears</p>
     * <p>"Manage Coupon Codes" tab is active</p>
     * <p>"Coupon Code" field is blank"</p>
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
        $this->agccHelper()->createRuleAndContinueEdit($ruleData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->verifyForm($verificationData['info'], 'rule_information');
        //Steps
        $this->openTab('manage_coupon_codes');
        $fieldXpath = $this->_getControlXpath('field', 'coupon_qty');
        //Verification
        if (!$this->isEditable($fieldXpath)) {
            $this->fail('Manage Coupon Codes tab is not active');
        }
    }

    /**
     * <p>Creating SCPR with activating Auto Generated Specific Coupon Codes functionality negative test</p>
     * <p>Steps:</p>
     * <p>1. Press button "Add New Rule"</p>
     * <p>1. Select value "Specific Coupon" in "Coupon" dropdown</p>
     * <p>2. Left "Coupon Code" field blank</p>
     * <p>4. Press button "Save and Continue Edit"</p>
     * <p>Expected result:</p>
     * <p>Received the validation message "This is required field." under "Coupon Code" field.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3760
     */
    public function createWithAgccNegative()
    {
        //Data
        $ruleData = $this->loadDataSet('Agcc', 'scpr_required_fields_with_agcc_negative');
        //Steps
        $this->agccHelper()->createRuleAndContinueEdit($ruleData);
        //Verification
        $this->assertMessagePresent('validation', 'this_is_a_required_field');
    }

    /**
     * <p>Deactivating Auto Generated Specific Coupon Codes functionality in SCPR</p>
     * <p>Steps:</p>
     * <p>1. Press button "Add New Rule"</p>
     * <p>2. Fill all required fields</p>
     * <p>3. Set "Use Auto Generation" checkbox to "Yes"</p>
     * <p>4. Press button "Save and Continue Edit"</p>
     * <p>5. Set "Use Auto Generation" checkbox to "No"</p>
     * <p>6. Fill any value to "Coupon Code" field</p>
     * <p>7. Press button "Save and Continue Edit"</p>
     * <p>Expected result:</p>
     * <p>Received the message that The rule has been saved.</p>
     * <p>Checkbox "Use Auto Generation" remains set to "No"</p>
     * <p>"Manage Coupon Codes" tab remains</p>
     * <p>"Manage Coupon Codes" tab is inactive</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3761
     */
    public function deactivateManageCouponCodesTab()
    {
        //Data
        $ruleData = $this->loadDataSet('Agcc', 'scpr_required_fields_with_agcc');
        //Steps
        $this->agccHelper()->createRuleAndContinueEdit($ruleData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->verifyForm($ruleData['info'], 'rule_information');
        //Steps
        $this->openTab('manage_coupon_codes');
        $fieldXpath = $this->_getControlXpath('field', 'coupon_qty');
        //Verification
        if ($this->isEditable($fieldXpath)) {
            //Data
            $ruleData = $this->loadDataSet('Agcc', 'scpr_required_fields_deactivate_agcc');
            //Steps
            $this->priceRulesHelper()->fillTabs($ruleData);
            $this->saveForm('save_and_continue_edit');
            //Verification
            $this->assertMessagePresent('success', 'success_saved_rule');
            //Steps
            $this->openTab('manage_coupon_codes');
            $fieldXpath = $this->_getControlXpath('field', 'coupon_qty');
            //Verification
            if ($this->isEditable($fieldXpath)) {
                $this->fail('Manage Coupon Codes tab is active');
            }
        }
        else {
            $this->fail('Manage Coupon Codes tab is not active');
        }
    }
}