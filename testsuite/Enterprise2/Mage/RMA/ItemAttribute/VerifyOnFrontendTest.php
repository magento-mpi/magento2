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

class Enterprise2_Mage_RMA_ItemAttribute_VerifyOnFrontendTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('RMA/enable_rma_on_frontend');
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
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $user = array('email' => $userData['email'], 'password' => $userData['password']);
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'signedin_flatrate_checkmoney',
            array('general_name' => $simple['general_name']));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->customerHelper()->frontLoginCustomer($user);
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderShipmentHelper()->openOrderAndCreateShipment(array('filter_order_id' => $orderId));

        return array( 'user'     => $user,
                      'order_id' => $orderId);
    }

    /**
     * <p> Verify that Custom RMA Item Attribute is show on frontend(Show on Frontend = Yes)</p>
     * <p>Preconditions</p>
     * <p>1. Customer is registered</p>
     * <p>2. Order placed and shipment created</p>
     * <p>Steps</p>
     * <p>1. Login to Backend</p>
     * <p>2. Navigate to Sales-RMA-Manage RMA Items Attributes<p>
     * <p>3. Create new RMA attribute with 'Input Type' = 'Text field','Show on Frontend' = 'Yes' and 'Form to Use' = 'Default EAV Form'</p>
     * <p>4. Login to Frontend</p>
     * <p>5. Open "View Order" page for order from preconditions</p>
     * <p>6. Click "Return" button</p>
     * <p>Expected result</p>
     * <p>1. New attribute present on Frontend</p>
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
        $this->addParameter('param', '0');
        $this->addParameter('attributeCode', $attrData['attribute_code']);
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->frontend('my_orders_history');
        $this->clickControl('link', 'view_order');
        $this->clickControl('link', 'return');
        //Verification
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
     * <p>Preconditions</p>
     * <p>1. Customer is registered</p>
     * <p>2. Order placed and shipment created</p>
     * <p>Steps</p>
     * <p>1. Login to Backend</p>
     * <p>2. Navigate to Sales-RMA-Manage RMA Items Attributes<p>
     * <p>3. Create new RMA attribute with 'Input Type' = 'Text field','Show on Frontend' = 'No' and 'Form to Use' = 'Default EAV Form'</p>
     * <p>4. Login to Frontend</p>
     * <p>5. Open "View Order" page for order from preconditions</p>
     * <p>6. Click "Return" button</p>
     * <p>Expected result</p>
     * <p>1. New attribute is missing on Frontend</p>
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
        $this->addParameter('param', '0');
        $this->addParameter('attributeCode', $attrData['attribute_code']);
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->frontend('my_orders_history');
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
     * <p>Preconditions</p>
     * <p>1. Customer is registered</p>
     * <p>2. Order placed and shipment created</p>
     * <p>Steps</p>
     * <p>1. Login to Backend</p>
     * <p>2. Navigate to Sales-RMA-Manage RMA Items Attributes<p>
     * <p>3. Open 'Resolution' attribute</p>
     * <p>4. Set 'Show on Frontend' = 'No' and save attribute</p>
     * <p>5. Login to Frontend</p>
     * <p>6. Open "View Order" page for order from preconditions</p>
     * <p>7. Click "Return" button</p>
     * <p>8. Repeat steps 2-7 with other system attribute: Item Condition, Reason to Return</p>
     * <p>Expected result</p>
     * <p>1. Attribute is missing on Frontend</p>
     * <p>Postconditions</p>
     * <p>1. Set 'Show on Frontend' = 'Yes' in previously edited attribute</p>
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
        $this->addParameter('attribute_admin_title', $attributeLabel);
        $this->addParameter('orderId', $testData['order_id']);
        $this->addParameter('param', '0');
        //Steps
        $this->searchAndOpen(array ('attribute_label' => $attributeLabel));
        $this->fillDropdown('show_on_frontend', 'No');
        $this->clickButton('save_attribute');
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->frontend('my_orders_history');
        $this->clickControl('link', 'view_order');
        $this->clickControl('link', 'return');
        //Verification
        $this->assertFalse($this->controlIsPresent('dropdown', $attributeName), 'System RMA attribute must be absent');
        //Postcondition
        $this->loginAdminUser();
        $this->navigate('manage_rma_items_attribute');
        $this->searchAndOpen(array ('filter_attribute_label' => $attributeLabel));
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
