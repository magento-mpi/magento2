<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Order
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Creating order for new customer with one required field empty
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Order_Create_CheckingValidationTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     *
     * <p>Log in to Backend.</p>
     *
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('PaymentMethod/savedcc_without_3Dsecure');
    }

    protected function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Creating Simple product</p>
     *
     * @return string
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');

        return $simple['general_name'];
    }

    /**
     * <p>Create customer via 'Create order' form (required fields are not filled).</p>
     *
     * @param string $emptyField
     * @param string $simpleSku
     *
     * @test
     * @dataProvider emptyRequiredFieldsInBillingAddressDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3253
     */
    public function emptyRequiredFieldsInBillingAddress($emptyField, $simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_physical', array('filter_sku' => $simpleSku));
        if ($emptyField != 'billing_country') {
            $orderData['billing_addr_data'] = $this->loadDataSet('SalesOrder', 'billing_address_req_usa',
                array($emptyField => ''));
        } else {
            $orderData['billing_addr_data'] = $this->loadDataSet('SalesOrder', 'billing_address_req_usa',
                array($emptyField => '', 'billing_state' => '%noValue%'));
        }
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $page = $this->getUimapPage('admin', 'create_order_for_new_customer');
        $fieldSet = $page->findFieldset('order_billing_address');
        if ($emptyField != 'billing_country' and $emptyField != 'billing_state') {
            $fieldXpath = $fieldSet->findField($emptyField);
        } else {
            $fieldXpath = $fieldSet->findDropdown($emptyField);
        }
        if ($emptyField == 'billing_street_address_1') {
            $fieldXpath .= "/ancestor::div[@class='multi-input']";
        }
        $this->addParameter('fieldXpath', $fieldXpath);

        $this->assertMessagePresent('error', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function emptyRequiredFieldsInBillingAddressDataProvider()
    {
        return array(
            array('billing_first_name'),
            array('billing_last_name'),
            array('billing_street_address_1'),
            array('billing_city'),
            array('billing_country'),
            array('billing_state'),
            array('billing_zip_code'),
            array('billing_telephone')
        );
    }

    /**
     * Fails due to MAGE-5616
     * <p>Create customer via 'Create order' form (required fields are not filled).</p>
     *
     * @param string $emptyField
     * @param string $simpleSku
     *
     * @test
     * @dataProvider emptyRequiredFieldsInShippingAddressDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3254
     */
    public function emptyRequiredFieldsInShippingAddress($emptyField, $simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_physical', array('filter_sku' => $simpleSku));
        if ($emptyField != 'shipping_country') {
            $orderData['shipping_addr_data'] = $this->loadDataSet('SalesOrder', 'shipping_address_req_usa',
                array($emptyField => ''));
        } else {
            $orderData['shipping_addr_data'] = $this->loadDataSet('SalesOrder', 'shipping_address_req_usa',
                array($emptyField => '', 'shipping_state' => '%noValue%'));
        }
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        //Verifying
        $page = $this->getUimapPage('admin', 'create_order_for_new_customer');
        $fieldSet = $page->findFieldset('order_shipping_address');
        if ($emptyField != 'shipping_country' and $emptyField != 'shipping_state') {
            $fieldXpath = $fieldSet->findField($emptyField);
        } else {
            $fieldXpath = $fieldSet->findDropdown($emptyField);
        }
        if ($emptyField == 'shipping_street_address_1') {
            $fieldXpath .= "/ancestor::div[@class='multi-input']";
        }
        $this->addParameter('fieldXpath', $fieldXpath);

        $this->assertMessagePresent('error', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function emptyRequiredFieldsInShippingAddressDataProvider()
    {
        return array(
            array('shipping_first_name'),
            array('shipping_last_name'),
            array('shipping_street_address_1'),
            array('shipping_city'),
            array('shipping_country'),
            array('shipping_state'),
            array('shipping_zip_code'),
            array('shipping_telephone')
        );
    }

    /**
     * <p>Create order without shipping method</p>
     *
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3258
     */
    public function withoutGotShippingMethod($simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simpleSku));
        unset($orderData['shipping_data']);
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        //Verifying
        $fieldXpath = $this->_getControlXpath('link', 'get_shipping_methods_and_rates');
        $this->addParameter('fieldXpath', $fieldXpath);
        $this->assertMessagePresent('error', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Create order without shipping method</p>
     *
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3257
     */
    public function withGotShippingMethod($simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simpleSku));
        $billingAddress = $orderData['billing_addr_data'];
        $shippingAddress = $orderData['shipping_addr_data'];
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        $this->orderHelper()->addProductToOrder($orderData['products_to_add']['product_1']);
        $this->orderHelper()->fillOrderAddress($billingAddress, $billingAddress['address_choice'], 'billing');
        $this->orderHelper()->fillOrderAddress($shippingAddress, $shippingAddress['address_choice'], 'shipping');
        $this->orderHelper()->selectPaymentMethod($orderData['payment_data']);
        $this->clickControl('link', 'get_shipping_methods_and_rates', false);
        $this->pleaseWait();
        $this->orderHelper()->submitOrder();
        //Verifying
        $this->assertMessagePresent('error', 'shipping_must_be_specified');
    }

    /**
     * <p>Create order without products.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3256
     */
    public function noProductsChosen()
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        //Verifying
        $this->assertMessagePresent('error', 'error_specify_order_items');
        $this->assertMessagePresent('error', 'shipping_must_be_specified');
    }

    /**
     * <p>Create order without payment method.</p>
     *
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3255
     */
    public function noPaymentMethodChosen($simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simpleSku));
        unset($orderData['payment_data']);
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        //Verifying
        $this->assertMessagePresent('error', 'empty_payment_method');
    }

    /**
     * <p>Test for credit card with all empty fields</p>
     *
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     */
    public function emptyAllCardFieldsInSavedCCVisa($simpleSku)
    {
        //Data
        $paymentInfo = $this->loadDataSet('Payment', 'saved_empty_all');
        $paymentData = $this->loadDataSet('Payment', 'payment_savedcc', array('payment_info' => $paymentInfo));
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simpleSku, 'payment_data' => $paymentData));
        $emptyFields = array('card_verification_number' => 'field', 'expiration_year' => 'dropdown',
                             'name_on_card'             => 'field', 'card_type'       => 'dropdown');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        foreach ($emptyFields as $fieldName => $fieldType) {
            $xpath = $this->_getControlXpath($fieldType, $fieldName);
            $this->addParameter('fieldXpath', $xpath);
            $this->assertMessagePresent('validation', 'empty_required_field');
        }
        $this->assertTrue($this->verifyMessagesCount(4), $this->getParsedMessages());
    }

    /**
     * <p>Test for empty 'Name On Card' field in credit card visa</p>
     *
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     */
    public function emptyNameOnCardFieldInSavedCC($simpleSku)
    {
        //Data
        $paymentData = $this->loadDataSet('Payment', 'payment_savedcc');
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku'  => $simpleSku, 'payment_data' => $paymentData, 'name_on_card' => ''));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $xpath = $this->_getControlXpath('field', 'name_on_card');
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Test for empty 'Card Type' field in credit card visa</p>
     *
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     */
    public function emptyCardTypeFieldInSavedCC($simpleSku)
    {
        //Data
        $paymentData = $this->loadDataSet('Payment', 'payment_savedcc');
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku'  => $simpleSku, 'payment_data' => $paymentData, 'card_type'    => ''));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $xpath = $this->_getControlXpath('dropdown', 'card_type');
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $xpath = $this->_getControlXpath('field', 'card_verification_number');
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('validation', 'invalid_cvv');
        $this->assertTrue($this->verifyMessagesCount(2), $this->getParsedMessages());
    }

    /**
     * <p>Test for empty 'Card Number' field in credit card visa</p>
     *
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     */
    public function emptyCardNumberFieldInSavedCC($simpleSku)
    {
        //Data
        $paymentData = $this->loadDataSet('Payment', 'payment_savedcc');
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku'  => $simpleSku, 'payment_data' => $paymentData, 'card_number'  => ''));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $xpath = $this->_getControlXpath('dropdown', 'card_type');
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('validation', 'card_type_doesnt_match');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Test for empty 'Expiration Year' field in credit card visa</p>
     *
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     */
    public function emptyExpirationYearFieldInSavedCC($simpleSku)
    {
        //Data
        $paymentData = $this->loadDataSet('Payment', 'payment_savedcc');
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku'     => $simpleSku, 'payment_data'    => $paymentData, 'expiration_year' => ''));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $xpath = $this->_getControlXpath('dropdown', 'expiration_year');
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Test for empty 'Card Verification Number' field in credit card visa</p>
     *
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     */
    public function emptyCardVerificationNumberFieldInSavedCC($simpleSku)
    {
        //Data
        $paymentData = $this->loadDataSet('Payment', 'payment_savedcc');
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku'               => $simpleSku, 'payment_data'             => $paymentData,
                  'card_verification_number' => ''));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $xpath = $this->_getControlXpath('field', 'card_verification_number');
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Test for empty 'Expiration Month' field in credit card visa</p>
     *
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     */
    public function emptyExpirationMonthFieldInSavedCC($simpleSku)
    {
        //Data
        $paymentData = $this->loadDataSet('Payment', 'payment_savedcc');
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku'      => $simpleSku, 'payment_data'     => $paymentData, 'expiration_month' => ''));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('error', 'invalid_exp_date');
    }
}