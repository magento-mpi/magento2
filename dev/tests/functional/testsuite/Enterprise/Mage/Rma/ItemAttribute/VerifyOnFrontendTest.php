<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_RMA
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Verify RMA item attribute on Frontend
 *
 * @package     Mage_RMA
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_Rma_ItemAttribute_VerifyOnFrontendTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('RMA/enable_rma_on_frontend');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_rma_items_attribute');
    }

    /**
     * Create User, Product and create order
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'signedin_flatrate_checkmoney_usa',
            array('general_name' => $simple['general_name']));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderShipmentHelper()->openOrderAndCreateShipment(array('filter_order_id' => $orderId));

        return array(
            'user' => array('email' => $userData['email'], 'password' => $userData['password']),
            'order_id' => $orderId
        );
    }

    /**
     * <p> Verify that Custom RMA Item Attribute is show on frontend(Show on Frontend = Yes)</p>
     *
     * @param array $testData
     * @param array $attributeType
     *
     * @depends preconditionsForTests
     * @dataProvider customAttributeShowOnFrontendDataProvider
     * @test
     * @TestlinkId TL-MAGE-6116
     */
    public function customAttributeShowOnFrontend($attributeType, $testData)
    {
        //Data
        $attrData = $this->loadDataSet('RMAItemsAttribute', $attributeType, array('show_on_frontend' => 'Yes'));
        $this->addParameter('orderId', $testData['order_id']);
        $this->addParameter('attributeCode', $attrData['attribute_code']);
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->frontend('my_orders_history');
        $this->addParameter('elementTitle', $testData['order_id']);
        $this->clickControl('link', 'view_order');
        $this->clickControl('link', 'return');
        //Verification
        $this->addParameter('param', 0);
        $this->assertTrue($this->controlIsPresent('pageelement', 'custom_items_attribute'),
            'Custom RMA attribute must be present');
    }

    public function customAttributeShowOnFrontendDataProvider()
    {
        return array(
            array('rma_item_attribute_textfield'),
            array('rma_item_attribute_textarea'),
            array('rma_item_attribute_dropdown'),
            array('rma_item_attribute_image')
        );
    }

    /**
     * <p> Verify that Custom RMA Item Attribute is not show on frontend(Show on Frontend = No)</p>
     *
     * @param array $testData
     * @param array $attributeType
     *
     * @depends preconditionsForTests
     * @dataProvider customAttributeNotShowOnFrontendDataProvider
     * @test
     * @TestlinkId TL-MAGE-6117
     */
    public function customAttributeNotShowOnFrontend($attributeType, $testData)
    {
        //Data
        $attrData = $this->loadDataSet('RMAItemsAttribute', $attributeType, array('show_on_frontend' => 'No'));
        $this->addParameter('orderId', $testData['order_id']);
        $this->addParameter('attributeCode', $attrData['attribute_code']);
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->frontend('my_orders_history');
        $this->addParameter('elementTitle', $testData['order_id']);
        $this->clickControl('link', 'view_order');
        $this->clickControl('link', 'return');
        //Verification
        $this->assertFalse($this->controlIsPresent('pageelement', 'custom_items_attribute'),
            'Custom RMA attribute must be absent');
    }

    public function customAttributeNotShowOnFrontendDataProvider()
    {
        return array(
            array('rma_item_attribute_textfield'),
            array('rma_item_attribute_textarea'),
            array('rma_item_attribute_dropdown'),
            array('rma_item_attribute_image')
        );
    }

    /**
     * <p> Verify that System RMA Item Attribute is not show on frontend(Show on Frontend = No)</p>
     *
     * @param array $attributeLabel
     * @param array $attributeName
     * @param array $testData
     *
     * @depends preconditionsForTests
     * @dataProvider systemAttributeNotShowOnFrontendDataProvider
     * @test
     * @TestlinkId TL-MAGE-6119
     */
    public function systemAttributeNotShowOnFrontend($attributeLabel, $attributeName, $testData)
    {
        //Data
        $this->addParameter('elementTitle', $attributeLabel);
        $this->addParameter('orderId', $testData['order_id']);
        //Steps
        $this->searchAndOpen(array('filter_attribute_label' => $attributeLabel), 'rma_item_atribute_grid');
        $this->fillDropdown('show_on_frontend', 'No');
        $this->clickButton('save_attribute');
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->frontend('my_orders_history');
        $this->addParameter('elementTitle', $testData['order_id']);
        $this->clickControl('link', 'view_order');
        $this->clickControl('link', 'return');
        //Verification
        $this->assertFalse($this->controlIsPresent('dropdown', $attributeName), 'System RMA attribute must be absent');
        //Postcondition
        $this->loginAdminUser();
        $this->navigate('manage_rma_items_attribute');
        $this->addParameter('elementTitle', $attributeLabel);
        $this->searchAndOpen(array('filter_attribute_label' => $attributeLabel), 'rma_item_atribute_grid');
        $this->fillDropdown('show_on_frontend', 'Yes');
        $this->clickButton('save_attribute');
        $this->assertMessagePresent('success', 'success_saved_attribute');
    }

    public function systemAttributeNotShowOnFrontendDataProvider()
    {
        return array(
            array('Resolution', 'resolution'),
            array('Item Condition', 'condition'),
            array('Reason to Return', 'reason')
        );
    }
}
