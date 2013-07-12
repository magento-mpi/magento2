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
class Core_Mage_Agcc_ManageCouponCodesTest extends Mage_Selenium_TestCase
{
    public function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadDataSet('Agcc', 'scpr_required_fields_with_agcc');
        $this->priceRulesHelper()->createRuleAndContinueEdit($ruleData);
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->openTab('manage_coupon_codes');
        $this->fillField('coupon_qty', '9');
        $this->clickButton('generate');
        $this->pleaseWait();
    }

    /**
     * <p>Coupon Codes deleting negative test</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3886
     */
    public function deleteCouponCodesNegative()
    {
        //Steps
        $this->fillDropdown('actions', 'Delete');
        $this->clickButton('submit', false);
        //Verifying
        $actualText = $this->alertText();
        $this->acceptAlert();
        $this->assertSame('Please select items.', $actualText,
            'actual and expected confirmation message does not match');
    }

    /**
     * <p>Coupon Codes deleting test</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3885
     */
    public function deleteCouponCodes()
    {
        //Steps
        $trLocator = $this->search(array('filter_used' => 'No'), 'manage_coupons_grid');
        $this->assertNotNull($trLocator, "We couldn't find any records.");
        $columnId = $this->getColumnIdByName('Coupon Code');
        $couponCode = trim($this->getChildElement($this->getElement($trLocator), '//td[' . $columnId . ']')->text());
        $xpathCouponCode = $this->formSearchXpath(array('filter_coupon_code' => $couponCode));
        $this->searchAndChoose(array('filter_coupon_code' => $couponCode), 'manage_coupons_grid');
        $this->fillDropdown('actions', 'Delete');
        $this->clickButtonAndConfirm('submit', 'confirmation_for_delete_coupons', false);
        $this->pleaseWait();
        //Verification
        if ($this->elementIsPresent($xpathCouponCode)) {
            $this->fail('Coupon code ' . $couponCode . ' is not deleted!');
        }
    }
}