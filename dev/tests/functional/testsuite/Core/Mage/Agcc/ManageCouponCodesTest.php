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
        $this->agccHelper()->createRuleWithGeneratedCouponCodes($ruleData, 9);
    }

    /**
     * <p>Coupon Codes deleting negative test</p>
     * <p>Steps:</p>
     * <p>1. Create SCPR </p>
     * <p>2. Generate few Coupon Code </p>
     * <p>3. Select "Delete" in "Action" dropdown</p>
     * <p>2. Press button "Submit"</p>
     * <p>Expected result:</p>
     * <p>Popup "Please select items." appears</p>
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
        $this->assertSame('Please select items.', $this->alertText(),
            'actual and expected confirmation message does not match');
    }

    /**
     * <p>Coupon Codes deleting test</p>
     * <p>Steps:</p>
     * <p>1. Create SCPR </p>
     * <p>2. Generate one Coupon Code </p>
     * <p>3. Select generated Coupon Code </p>
     * <p>4. Select "Delete" in "Action" dropdown</p>
     * <p>5. Press button "Submit"</p>
     * <p>5. Press button "OK" on popup</p>
     * <p>Expected result:</p>
     * <p>Selected Coupon Code deleted.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3885
     */
    public function deleteCouponCodes()
    {
        //Steps
        $trLocator = $this->search(array('No'), 'manage_coupons_grid');
        $this->assertNotNull($trLocator, 'No records found.');
        $columnId = $this->getColumnIdByName('Coupon Code');
        $couponCode = trim($this->getChildElement($this->getElement($trLocator), '//td[' . $columnId . ']')->text());
        $xpathCouponCode = $this->formSearchXpath(array('filter_coupon_code' => $couponCode));
        $this->searchAndChoose(array('filter_coupon_code' => $couponCode), 'manage_coupons_grid');
        $this->fillDropdown('actions', 'Delete');
        $this->clickButtonAndConfirm('submit', 'confirmation_for_delete', false);
        $this->pleaseWait();
        //Verification
        if ($this->elementIsPresent($xpathCouponCode)) {
            $this->fail('Coupon code ' . $couponCode . ' is not deleted!');
        }
    }
}