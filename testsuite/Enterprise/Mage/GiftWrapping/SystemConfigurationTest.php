<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_GiftWrapping
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Enabling and disabling gift option in system configuration in different scopes (default scope, website scope)
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_GiftWrapping_SystemConfigurationTest extends Mage_Selenium_TestCase
{
    public function assertPreconditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
    }

    protected function tearDownAfterTest()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_disable_all');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_use_default_per_website');
    }

    /**
     * @test
     * @return array
     */
    public function preconditionsForTests()
    {
        $giftWrapping = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        //Steps
        $simple1 = $this->productHelper()->createSimpleProduct();
        $simple2 = $this->productHelper()->createSimpleProduct();

        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrapping);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');

        return array('sku_1'  => $simple1['simple']['product_sku'], 'sku_2' => $simple2['simple']['product_sku'],
                     'design' => $giftWrapping['gift_wrapping_design']);
    }

    /**
     * <p>Enabling/Disabling Gift Messages and Gift Wrapping options on Default and Website scope</p>
     * <p>Steps:</p>
     * <p>1. Log into backend;</p>
     * <p>2. Go to Sales page (System Configuration - Sales - Sales - Gift Options);</p>
     * <p>3. Switch to website scope when needed;</p>
     * <p>4. Switch "Allow Gift Messages for Order Items", "Allow Gift Wrapping on Order Level",
     * "Allow Gift Wrapping for Order Items", "Allow Gift Receipt",
     * "Allow Printed Card", "Allow Gift Messages on Order Level" - all to "Yes"/"No".
     * <p>5. Save Configuration.</p>
     * <p>Expected Results:</p>
     * <p>1. Received the message "The configuration has been saved"</p>
     * <p>2. All changed field values are correctly set.</p>
     *
     * @param string $settings Name of the dataset with settings
     *
     * @test
     * @dataProvider changeGiftOptionsSettingsDataProvider
     * @TestlinkId TL-MAGE-829
     * @TestlinkId TL-MAGE-839
     * @TestlinkId TL-MAGE-841
     * @TestlinkId TL-MAGE-843
     * @group skip_due_to_bug1.12
     */
    public function changeGiftOptionsSettings($settings)
    {
        $settings = $this->loadDataSet('GiftMessage', $settings);
        $this->systemConfigurationHelper()->configure($settings);
        $this->verifyForm($settings['tab_1']['configuration']['gift_options']);
        $this->assertEmptyVerificationErrors();
    }

    public function changeGiftOptionsSettingsDataProvider()
    {
        return array(
            array('gift_wrapping_all_enable'),
            array('gift_message_all_enable'),
            array('gift_wrapping_all_disable'),
            array('gift_message_all_disable'),
            array('gift_wrapping_all_enable_on_website'),
            array('gift_message_all_enable_on_website'),
            array('gift_wrapping_all_disable_on_website'),
            array('gift_message_all_disable_on_website')
        );
    }

    /**
     * <p>Preconditions:</p>
     * <p>System -> Sales -> Gift Options (Default scope) -> Switch to "no" following options:</p>
     * <p>"Allow Gift Messages on Order Level";</p>
     * <p>"Allow Gift Messages for Order Items";</p>
     * <p>"Allow Gift Wrapping on Order Level";</p>
     * <p>"Allow Gift Wrapping for Order Items";</p>
     * <p>"Allow Gift Receipt";</p>
     * <p>"Allow Printed Card";</p>
     * <p>System -> Sales -> Gift Options (Website scope) -> Switch to "yes" following options:</p>
     * <p>"Allow Gift Messages on Order Level";</p>
     * <p>"Allow Gift Messages for Order Items";</p>
     * <p>"Allow Gift Wrapping on Order Level";</p>
     * <p>"Allow Gift Wrapping for Order Items";<p>
     * <p>"Allow Gift Receipt";</p>
     * <p>"Allow Printed Card";</p>
     *
     * <p>Steps:</p>
     * <p>1. Log into backend Sales -> Orders;</p>
     * <p>2. Push "create New Order";</p>
     * <p>3. Select any customer from list;</p>
     * <p>4. Select a Store from list;</p>
     * <p>5. Add at least 2 products using "Add products" button;</p>
     * <p>6. Enter Billing and shipping addresses;</p>
     * <p>7. Choose Shipping and payment Methods;</p>
     * <p>8. Edit gift messages for entire order and Items individually;</p>
     * <p>9. Push "Submit Order" button;</p>
     * <p>10. Open the created order. Check if all switched in this test case gift options are</p>
     * <p> saved;</p>
     *
     * <p>Expected result:</p>
     * <p>After step 9: Notification massage "The order has been created." appears</p>
     * <p>After step 10: All switched in this test case gift options are saved</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-861
     */
    public function giftWrappingBackendWebsite($testData)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_gift_options_full', null,
            array('product1'           => $testData['sku_1'], 'product2' => $testData['sku_1'],
                  'giftWrappingDesign' => $testData['design']));
        //Steps
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_enable_all_website');
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verification
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderHelper()->verifyGiftOptions($orderData);
    }

    /**
     * <p>Preconditions:</p>
     * <p>System -> Sales -> Gift Options (Default scope) -> Switch to "yes" following options:</p>
     * <p>"Allow Gift Messages on Order Level";</p>
     * <p>"Allow Gift Messages for Order Items";</p>
     * <p>"Allow Gift Wrapping on Order Level";</p>
     * <p>"Allow Gift Wrapping for Order Items";</p>
     * <p>"Allow Gift Receipt";</p>
     * <p>"Allow Printed Card";</p>
     * <p>System -> Sales -> Gift Options (Website scope) -> Switch to "no" following options:</p>
     * <p>"Allow Gift Messages on Order Level";</p>
     * <p>"Allow Gift Messages for Order Items";</p>
     * <p>"Allow Gift Wrapping on Order Level";</p>
     * <p>"Allow Gift Wrapping for Order Items";<p>
     * <p>"Allow Gift Receipt";</p>
     * <p>"Allow Printed Card";</p>
     *
     * <p>Steps:</p>
     * <p>1. Log into backend Sales-> Orders;</p>
     * <p>2. Push "create New Order";</p>
     * <p>3. Select any customer from list;</p>
     * <p>4. Select a Store from list;</p>
     * <p>5. Add at least 2 products using "Add products" button;</p>
     *
     * <p>Expected result:</p>
     * <p>After step 5: "Gift Options" link does not appear under any of the added products;</p>
     * <p>"Gift Options" are not available for the whole order;</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-872
     */
    public function giftWrappingBackendGlobalScope($testData)
    {
        //Preconditions
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_enable_all_default_config');
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_gift_options_full', null,
            array('product1' => $testData['sku_1'], 'product2' => $testData['sku_1']));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        $this->fillFieldset($orderData['account_data'], 'order_account_information');
        foreach ($orderData['products_to_add'] as $value) {
            $this->orderHelper()->addProductToOrder($value);
        }
        //Verification
        $this->orderHelper()->verifyGiftOptionsDisabled($orderData);
    }
}
