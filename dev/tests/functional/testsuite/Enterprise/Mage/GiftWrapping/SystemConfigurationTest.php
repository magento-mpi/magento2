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
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/disable_single_store_mode');
    }

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
     * @skipTearDown
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

        return array(
            'sku_1' => $simple1['simple']['product_sku'],
            'sku_2' => $simple2['simple']['product_sku'],
            'design' => $giftWrapping['gift_wrapping_design']
        );
    }

    /**
     * <p>Enabling/Disabling Gift Messages and Gift Wrapping options on Default and Website scope</p>
     *
     * @param string $settings Name of the dataset with settings
     *
     * @test
     * @dataProvider changeGiftOptionsSettingsDataProvider
     * @TestlinkId TL-MAGE-829
     * @TestlinkId TL-MAGE-839
     * @TestlinkId TL-MAGE-841
     * @TestlinkId TL-MAGE-843
     */
    public function changeGiftOptionsSettings($settings)
    {
        $this->systemConfigurationHelper()->configure('GiftMessage/' . $settings);
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
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-861
     */
    public function giftWrappingBackendWebsite($testData)
    {
        $this->markTestIncomplete('BUG: Gift Options for item apply to all');
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_gift_options_full', null, array(
            'product1' => $testData['sku_1'],
            'product2' => $testData['sku_2'],
            'giftWrappingDesign' => $testData['design']
        ));
        //Steps
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_enable_all_website');
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verification
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderHelper()->verifyGiftOptions($orderData['gift_messages']);
    }

    /**
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-872
     */
    public function giftWrappingBackendGlobalScope($testData)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_gift_options_full', null, array(
            'product1' => $testData['sku_1'],
            'product2' => $testData['sku_2']
        ));
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
