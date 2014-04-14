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
 * Orders And Returns
 *
 * @package     Mage_RMA
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_Rma_OrdersAndReturns_OrdersAndReturnsTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->frontend();
        $this->clickControl('link', 'orders_and_returns');
    }

    /**
     * Create product
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'guest_flatrate_checkmoney_usa',
            array('general_name' => $simple['general_name']));
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->frontend();
        $orderNumber = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $this->assertMessagePresent('success', 'success_checkout');

        return array(
            'order_id' => $orderNumber,
            'billing_last_name' => $checkoutData['billing_address_data']['billing_last_name'],
            'search_type_id' => 'Email Address',
            'email' => $checkoutData['billing_address_data']['billing_email'],
            'zip' => $checkoutData['billing_address_data']['billing_zip_code']
        );
    }

    /**
     * <p>Check elements on "Returns" page</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6017
     */
    public function navigationTest()
    {
        //Verifications
        $this->validatePage('orders_and_returns');
        $this->assertTrue($this->controlIsPresent('field', 'order_id'), 'There is no "Order ID" field on the page');
        $this->assertTrue($this->controlIsPresent('field', 'billing_last_name'),
            'There is no "Billing Last Name" field on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'search_type_id'),
            'There is no "Find Order By" dropdown on the page');
        $this->assertTrue($this->controlIsPresent('field', 'email'), 'There is no "Email Address" field on the page');
        $this->assertTrue($this->controlIsPresent('field', 'zip'), 'There is no "Billing ZIP Code" field on the page');
        $this->assertTrue($this->buttonIsPresent('continue'), 'There is no "Continue" button on the page');
    }

    /**
     * <p>Enter to OAR with empty field</p>
     *
     * @param array $testData
     * @param string $field
     *
     * @test
     * @depends preconditionsForTests
     * @depends navigationTest
     * @dataProvider emptyFieldsDataProvider
     * @TestlinkId TL-MAGE-6018
     */
    public function emptyFields($field, $testData)
    {
        //Data
        $testData[$field] = '';
        if ($field == 'zip') {
            $testData['search_type_id'] = 'ZIP Code';
            unset ($testData['email']);
        } else {
            unset ($testData['zip']);
        }
        //Steps
        $this->fillFieldset($testData, 'orders_and_returns_form');
        $this->clickControlAndWaitMessage('button', 'continue');
        //Verification
        $this->addFieldIdToMessage('field', $field);
        $this->assertMessagePresent('validation', 'empty_required_field');
    }

    public function emptyFieldsDataProvider()
    {
        return array(
            array('order_id'),
            array('billing_last_name'),
            array('email'),
            array('zip')
        );
    }

    /**
     * <p>Enter to OAR with wrong data</p>
     *
     * @param array $testData
     * @param string $field
     *
     * @test
     * @depends preconditionsForTests
     * @depends navigationTest
     * @dataProvider wrongValueDataProvider
     * @TestlinkId TL-MAGE-6019
     */
    public function wrongData($field, $testData)
    {
        //Data
        $testData[$field] = 'supermail@example.com';
        if ($field == 'zip') {
            $testData['search_type_id'] = 'ZIP Code';
            unset ($testData['email']);
        } else {
            unset ($testData['zip']);
        }
        //Steps
        $this->fillFieldset($testData, 'orders_and_returns_form');
        $this->clickControlAndWaitMessage('button', 'continue');
        //Verification
        $this->assertMessagePresent('error', 'incorrect_data');
    }

    public function wrongValueDataProvider()
    {
        return array(
            array('order_id'),
            array('billing_last_name'),
            array('email'),
            array('zip')
        );
    }

    /**
     * <p>Enter to OAR with correct data</p>
     *
     * @param array $testData
     * @param array $field
     *
     * @test
     * @depends preconditionsForTests
     * @depends navigationTest
     * @dataProvider correctValueDataProvider
     * @TestlinkId TL-MAGE-6020
     */
    public function correctData($field, $testData)
    {
        //Data
        if ($field == 'zip') {
            $testData['search_type_id'] = 'ZIP Code';
            unset ($testData['email']);
        } else {
            unset ($testData['zip']);
        }
        $this->addParameter('elementTitle', $testData['order_id']);
        //Steps
        $this->fillFieldset($testData, 'orders_and_returns_form');
        $this->clickButton('continue');
        //Verification
        $this->validatePage('guest_view_order');
    }

    public function correctValueDataProvider()
    {
        return array(
            array('email'),
            array('zip')
        );
    }
}
