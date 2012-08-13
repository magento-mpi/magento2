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
class Enterprise2_Mage_RMA_OrdersAndReturns_OrdersAndReturnsTest extends Mage_Selenium_TestCase
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
        $checkoutData =$this->loadDataSet('OnePageCheckout', 'guest_flatrate_checkmoney',
                                          array('general_name' => $simple['general_name']));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->frontend();
        $orderNumber = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $this->assertMessagePresent('success', 'success_checkout');

        return array('order_id'          => $orderNumber,
                     'billing_last_name' => $checkoutData['billing_address_data']['billing_last_name'],
                     'email'             => $checkoutData['billing_address_data']['billing_email'],
                     'zip'               => $checkoutData['billing_address_data']['billing_zip_code']);
    }

    /**
     * <p>Check elements on "Returns" page</p>
     * <p>Steps</p>
     * <p>1. Open Frontend</p>
     * <p>2. Click "Orders and Return" link in footer</p>
     * <p>Expected result</p>
     * <p>1. "Returns" page is open</p>
     * <p>2. Page contain: Order ID, Billing Last Name, Find Order By, Email Address fields and Continue button</p>
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
     * <p>Precondition</p>
     * <p>1. Order with simple product is created</p>
     * <p>Steps</p>
     * <p>1. Open Frontend</p>
     * <p>2. Click "Orders and Return" link in footer</p>
     * <p>3. Leave empty "Order ID" field and fill other fields</p>
     * <p>4. Click button "Continue"</p>
     * <p>5. Repeat steps 3-4 with other fields</p>
     * <p>Expected result</p>
     * <p>1. Show message "This is a required field."</p>
     *
     * @param array $testData
     * @param array $field
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
            $testData['search_type_id'] = 'Email Address';
            unset ($testData['zip']);
        }
        //Steps
        $this->fillFieldset($testData, 'orders_and_returns_form');
        $this->clickControlAndWaitMessage('button', 'continue');
        $message = 'empty_' . $field;
        //Verification
        $this->assertMessagePresent('error', $message);
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
     * <p>Precondition</p>
     * <p>1. Order with simple product is created</p>
     * <p>Steps</p>
     * <p>1. Open Frontend</p>
     * <p>2. Click "Orders and Return" link in footer</p>
     * <p>3. Enter wrong value in "Order ID" field and fill correct data in other fields</p>
     * <p>4. Click button "Continue"</p>
     * <p>5. Repeat steps 3-4 with other fields</p>
     * <p>Expected result</p>
     * <p>1. Show message "Entered data is incorrect. Please try again."</p>
     *
     * @param array $testData
     * @param array $field
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
            $testData['search_type_id'] = 'Email Address';
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
     * <p>Precondition</p>
     * <p>1. Order with simple product is created</p>
     * <p>Steps</p>
     * <p>1. Open Frontend</p>
     * <p>2. Click "Orders and Return" link in footer</p>
     * <p>3. Select "Email Address" in "Find Order By" field</p>
     * <p>3. Enter correct data in all fields </p>
     * <p>4. Click button "Continue"</p>
     * <p>5. Repeat case used "ZIP Code" in "Find Order By" field</p>
     * <p>Expected result</p>
     * <p>1. "Order information for guest" page is open</p>
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
            $testData['search_type_id'] = 'Email Address';
            unset ($testData['zip']);
        }
        $this->addParameter('orderId', $testData['order_id']);
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
