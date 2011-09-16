<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Order_Helper extends Mage_Selenium_TestCase
{

    /**
     * Generates array of strings for filling customer's billing/shipping form
     * @param string $charsType :alnum:, :alpha:, :digit:, :lower:, :upper:, :punct:
     * @param string $addrType Gets two values: 'billing' and 'shipping'.
     *                         Default is 'billing'
     * @param int $symNum min = 5, default value = 32
     * @return array
     * @uses DataGenerator::generate()
     * @see DataGenerator::generate()
     */
    public function customerAddressGenerator($charsType, $addrType = 'billing', $symNum = 32, $required = FALSE)
    {
        $type = array(':alnum:', ':alpha:', ':digit:', ':lower:', ':upper:', ':punct:');
        if (array_search($charsType, $type) === FALSE
                || ($addrType != 'billing' && $addrType != 'shipping')
                || $symNum < 5 || !is_int($symNum)) {
            throw new Exception('Incorrect parameters');
        } else {
            if ($required == TRUE) {
                return array(
                    $addrType . '_address_choice'          => 'Add New Address',
                    $addrType . '_prefix'                  => '%noValue%',
                    $addrType . '_first_name'              => $this->generate('string', $symNum, $charsType),
                    $addrType . '_middle_name'             => '%noValue%',
                    $addrType . '_last_name'               => $this->generate('string', $symNum, $charsType),
                    $addrType . '_suffix'                  => '%noValue%',
                    $addrType . '_company'                 => '%noValue%',
                    $addrType . '_street_address_1'        => $this->generate('string', $symNum, $charsType),
                    $addrType . '_street_address_2'        => '%noValue%',
                    $addrType . '_region'                  => '%noValue%',
                    $addrType . '_city'                    => $this->generate('string', $symNum, $charsType),
                    $addrType . '_zip_code'                => $this->generate('string', $symNum, $charsType),
                    $addrType . '_telephone'               => $this->generate('string', $symNum, $charsType),
                    $addrType . '_fax'                     => '%noValue%'
                );
            } else {
                return array(
                    $addrType . '_address_choice'          => 'Add New Address',
                    $addrType . '_prefix'                  => $this->generate('string', $symNum, $charsType),
                    $addrType . '_first_name'              => $this->generate('string', $symNum, $charsType),
                    $addrType . '_middle_name'             => $this->generate('string', $symNum, $charsType),
                    $addrType . '_last_name'               => $this->generate('string', $symNum, $charsType),
                    $addrType . '_suffix'                  => $this->generate('string', $symNum, $charsType),
                    $addrType . '_company'                 => $this->generate('string', $symNum, $charsType),
                    $addrType . '_street_address_1'        => $this->generate('string', $symNum, $charsType),
                    $addrType . '_street_address_2'        => $this->generate('string', $symNum, $charsType),
                    $addrType . '_region'                  => $this->generate('string', $symNum, $charsType),
                    $addrType . '_city'                    => $this->generate('string', $symNum, $charsType),
                    $addrType . '_zip_code'                => $this->generate('string', $symNum, $charsType),
                    $addrType . '_telephone'               => $this->generate('string', $symNum, $charsType),
                    $addrType . '_fax'                     => $this->generate('string', $symNum, $charsType)
                );
            }
        }
    }

    /**
     * Creates order
     * @param array|string $orderData        Array or string with name of dataset to load
     * @param bool         $validate         If $validate == TRUE 'Submit Order' button will not be pressed
     *
     * @return bool|string
     */
    public function createOrder($orderData, $validate = FALSE)
    {
        $this->addParameter('id', '0');
        if (is_string($orderData)) {
            $orderData = $this->loadData($orderData);
        }
        $orderData = $this->arrayEmptyClear($orderData);
        if (isset($orderData['store_view'])) {
            $this->addParameter('storeName', $orderData['store_view']);
        }
        if (isset($orderData['customer_data'])) {
            $this->navigateToCreateOrderPage($orderData['customer_data']);
        } else {
            $this->navigateToCreateOrderPage(NULL);
        }
        if (isset($orderData['products_to_add'])) {
            $this->addProductsToOrder($orderData['products_to_add']);
        }
        if (isset($orderData['products_to_reconfigure'])) {
            $this->reconfigProduct($orderData['products_to_reconfigure']);
        }
        if (isset($orderData['gift_messages'])) {
            $this->addGiftMessage($orderData['gift_messages']);
        }
        if (isset($orderData['coupons'])) {
            $this->applyCoupon($orderData['coupons']);
        }
        if (isset($orderData['account_data'])) {
            $this->fillForm($orderData['account_data'], 'order_account_information');
        }
        if (isset($orderData['billing_addr_data'])) {
            if (isset($orderData['billing_addr_data']['billing_address_choice']) &&
                    ($orderData['billing_addr_data']['billing_address_choice'] == 'Add New Address')) {
                $this->fillOrderAddress('new', 'billing',  $orderData['billing_addr_data']);
            } elseif (isset($orderData['billing_addr_data']['billing_address_choice']) &&
                    ($orderData['billing_addr_data']['billing_address_choice'] != 'Add New Address')){
                $this->fillOrderAddress('exist', 'billing',  $orderData['billing_addr_data']);
            }
        }
        if (isset($orderData['shipping_addr_data'])) {
            if ($orderData['shipping_addr_data']['shipping_same_as_billing_address'] == 'yes'){
                $this->fillOrderAddress('sameAsBilling');
            } else {
                if (isset($orderData['shipping_addr_data']['shipping_address_choice']) &&
                        ($orderData['shipping_addr_data']['shipping_address_choice'] == 'Add New Address')){
                    $this->fillOrderAddress('new', 'shipping',  $orderData['shipping_addr_data']);
                } elseif (isset($orderData['shipping_addr_data']['shipping_address_choice']) &&
                        ($orderData['shipping_addr_data']['shipping_address_choice'] != 'Add New Address')){
                    $this->fillOrderAddress('exist', 'shipping',  $orderData['shipping_addr_data']);
                }
            }
        }
        if (isset($orderData['payment_data'])) {
            $this->selectPaymentMethod($orderData['payment_data']);
        }
        if (isset($orderData['shipping_data'])) {
            $this->selectShippingMethod($orderData['shipping_data']);
        }
        if ($validate == TRUE) {
            $this->clickButton('submit_order', FALSE);
            $this->pleaseWait();
            return FALSE;
        } else {
            $errors = $this->getErrorMessages();
            $this->assertTrue(empty($errors), $this->messages);
            $this->saveForm('submit_order');
            if ($this->successMessage('success_created_order') == TRUE) {
                return $this->_defineOrderId('view_order');
            }
            return FALSE;
        }
    }

    /**
     * Fills customer's addresses at the order page.
     *
     * @param string $addressType   'new', 'exist', 'sameAsBilling'
     * @param string $address       'billing' or 'shipping'
     * @param array  $addressData
     */
    public function fillOrderAddress($addressType, $address = NULL, $addressData = NULL)
    {
        if ($addressType == 'sameAsBilling') {
            $this->fillForm(array('shipping_same_as_billing_address' => 'yes'));
        }
        if (($addressType == 'new') && ($address != NULL) && ($addressData != NULL)) {
            if ($addressData[$address.'_address_choice'] == 'Add New Address')
                $this->fillForm($addressData);
        }
        if (($addressType == 'exist') && ($address != NULL) && ($addressData != NULL)) {
            $this->fillForm(array($address.'_address_choice' => $this->defineAddressToChoose($addressData)));
        }
    }

    /**
     * Returns address that was found and can be selected from existing customer addresses.
     *
     * @param array         $keyWords
     *
     * @return bool|string               The most suitable address found by using keywords
     */
    public function defineAddressToChoose(array $keyWords)
    {
        foreach ($keyWords as $key => $value) {
            if (preg_match('/prefix/', $key) || preg_match('/middle/', $key)
                    || preg_match('/suffix/', $key) || preg_match('/company/', $key)
                    || preg_match('/telephone/', $key) || preg_match('/fax/', $key)
                    || preg_match('/address_choice/', $key) || preg_match('/save_in_address_book/', $key)
                    || preg_match('/same_as_billing/', $key)) {
                unset ($keyWords[$key]);
            }
        }
        $xpathDropDown = $this->_getControlXpath('dropdown', 'billing_address_choice');
        $addressCount = $this->getXpathCount($xpathDropDown . '/option');
        $res = 0;
        for ($i = 1; $i <= $addressCount; $i++) {
            $addressValue = $this->getText($xpathDropDown . "/option[$i]");
            foreach ($keyWords as $v) {
                $res += preg_match('/'.preg_quote($v).'/', $addressValue);
            }
            if ($res == count($keyWords)) {
                $res = $addressValue;
                break;
            }
            $res = 0;
        }
        if (is_string($res)) {
            return $res;
        } else {
            return FALSE;
        }
    }

    /**
     * Defines order id
     *
     * @param string $fieldset
     * @return bool|integer
     */
    protected function _defineOrderId($fieldset)
    {
        try {
            $item_id = 0;
            $title_arr = explode('/', $this->getTitle());
            $item_id = preg_replace("/^[^0-9]?(.*?)[^0-9]?$/i", "$1", $title_arr[0]);
            return $item_id;
        } catch (Exception $e) {
            $this->_error = TRUE;
            return FALSE;
        }
    }

    /**
     * Orders product during forming order
     *
     * @param array $productData Product in array to add to order. Function should be called for each product to add
     */
    public function addProductsToOrder(array $productData)
    {
        $this->clickButton('add_products', FALSE);
        $configurable = FALSE;
        foreach ($productData as $product => $data) {
            $xpathProduct = $this->search($data);
            $this->assertNotEquals(NULL, $xpathProduct);
            if (!($this->isElementPresent($xpathProduct . "//a[text()='Configure'][@disabled]"))) {
                $configurable = TRUE;
            } else {
                $configurable = FALSE;
            }
            $this->searchAndChoose(array('filter_sku' => $productData[$product]['filter_sku']));
            if (array_key_exists('configurable_options', $data) && $configurable == TRUE) {
                $this->pleaseWait();
                $this->configureProduct($data);
                $this->clickButton('ok', FALSE);
            }
        }
        $this->clickButton('add_selected_products_to_order', FALSE);
        $this->pleaseWait();
    }

    /**
     * Configuring product when placing to order.
     *
     * @param array $productData Product in array to add to order. Function should be called for each product to add
     */
    public function configureProduct(array $productData)
    {
        foreach ($productData['configurable_options'] as $option => $value) {
            if (is_array($value)){
                foreach ($value as $clue => $dataset) {
                    if (preg_match('/value/', $clue)) {
                        $this->addParameter($option, $dataset);
                    } else {
                        $this->fillForm($dataset);
                    }
                }
            }
        }
    }

    /**
     * The way customer will pay for the order
     *
     * @param array|string $paymentMethod
     */
    public function selectPaymentMethod($paymentMethod)
    {
        if (is_string($paymentMethod)) {
            $paymentMethod = $this->loadData($paymentMethod);
        }
        if (!is_array($paymentMethod) && !is_string($paymentMethod)) {
            throw new Exception('Incorrect type of $paymentMethod.');
        }
        $this->pleaseWait();
        $this->clickControl('radiobutton', $paymentMethod['payment_method'], FALSE);
        $this->pleaseWait();
        $this->waitForAjax();
        if (array_key_exists('payment_info', $paymentMethod)) {
            $this->fillForm($paymentMethod['payment_info'], 'order_payment_method');
        }
        if (array_key_exists('3d_secure_validation_code', $paymentMethod)) {
            $this->clickButton('start_reset_validation', FALSE);
            $this->waitForAjax();
            $xpath = $this->_getControlXpath('button', '3d_password');
            $this->waitForElement($xpath);
            $this->type($xpath, $paymentMethod['3d_secure_validation_code']);
            $xpath = $this->_getControlXpath('button', '3d_submit');
            $this->clickButton('3d_submit', FALSE);
            $this->waitForElementNotPresent($xpath);
            $this->pleaseWait();
        }
    }

    /**
     * The way to ship the order
     *
     * @param array|string $shippingMethod
     */
    public function selectShippingMethod($shippingMethod)
    {
        if (is_string($shippingMethod)) {
            $shippingMethod = $this->loadData($shippingMethod);
        }
        if (array_key_exists('shipping_service', $shippingMethod) &&
                array_key_exists('shipping_method', $shippingMethod)){
                    $this->clickControl('link', 'get_shipping_methods_and_rates', FALSE);
                    $this->pleaseWait();
                    $this->addParameter('shipService', $shippingMethod['shipping_service']);
                    $this->addParameter('shipMethod', $shippingMethod['shipping_method']);
                    $this->clickControl('radiobutton', 'ship_method', FALSE);
                    $this->pleaseWait();
        }
    }

    /**
     * Gets to 'Create new Order page'
     *
     * @param array|string $customerData    Array with customer data to search or string with dataset to load
     */
    public function navigateToCreateOrderPage($customerData = NULL)
    {
        $this->clickButton('create_new_order');
        if ($customerData == NULL) {
            $this->clickButton('create_new_customer', FALSE);
        } else {
            if (is_string($customerData)) {
                $customerData = $this->loadData($customerData);
            }
            $this->assertNotEquals(NULL, $this->searchAndOpen($customerData, FALSE, 'order_customer_grid'));
        }
        $this->pleaseWait();
        $page = $this->getCurrentLocationUimapPage();
        $storeSelectorXpath = $page->findFieldset('order_store_selector')->getXpath();
        if ($this->isElementPresent($storeSelectorXpath . "[not(normalize-space(@style)='display:none')]")) {
            $this->clickControl('radiobutton', 'choose_main_store', FALSE);
            $this->pleaseWait();
        }
    }

    /**
     * Reconfigure already added to order products (change quantity, add discount, etc)
     *
     * @param array $reconfigProduct Array with the products and data to reconfigure
     */
    public function reconfigProduct(array $reconfigProduct)
    {
        foreach($reconfigProduct as $product => $options) {
            if (array_key_exists('filter_sku', $options)) {
                $this->addParameter('sku', $options['filter_sku']);
                if (array_key_exists('reconfigurable_options', $options)) {
                    $this->fillForm($options['reconfigurable_options']);
                }
            }
        }
        $this->clickButton('update_items_and_quantity', FALSE);
        $this->pleaseWait();
    }

    /**
     * Adding gift messaged to products during creating order at the backend.
     *
     * @param array $giftMessages Array with the gift messages for the products
     */
    public function addGiftMessage(array $giftMessages)
    {
        foreach($giftMessages as $product => $message) {
            if (array_key_exists('general_sku', $message)) {
                $this->addParameter('sku', $message['general_sku']);
                if (array_key_exists('message_options', $message)) {
                    $this->clickControl('link', 'gift_options', FALSE);
                    $this->waitForAjax();
                    $this->fillForm($message['message_options']);
                    $this->clickButton('ok', FALSE);
                    $this->pleaseWait();
                }
            }
        }
    }

    /**
     * Applying coupon for the products in order
     *
     * @param array $couponCode
     */
    public function applyCoupon(array $couponCode)
    {
        foreach ($couponCode as $coupon => $data)
        {
            $this->fillForm(array('coupon_code' => $data['coupon_code']));
            $this->clickButton('apply', FALSE);
            $this->pleaseWait();
            if ((strtolower($data['success']) == 'true') || (strtolower($data['success']) == '1')) {
                if ($this->successMessage('success_applying_coupon')) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            } else {
                if ($this->errorMessage('invalid_coupon_code')) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        }
    }
}
