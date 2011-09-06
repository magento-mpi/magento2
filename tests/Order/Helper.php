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
    public function customerAddressGenerator($charsType, $addrType = 'billing', $symNum = 32, $required = false)
    {
        $type = array(':alnum:', ':alpha:', ':digit:', ':lower:', ':upper:', ':punct:');
        if (array_search($charsType, $type) === false
                || ($addrType != 'billing' && $addrType != 'shipping')
                || $symNum < 5 || !is_int($symNum)) {
            throw new Exception('Incorrect parameters');
        } else {
            if ($required == true) {
                return array(
                    $addrType . '_prefix'           => '%noValue%',
                    $addrType . '_first_name'       => $this->generate('string', $symNum, $charsType),
                    $addrType . '_middle_name'      => '%noValue%',
                    $addrType . '_last_name'        => $this->generate('string', $symNum, $charsType),
                    $addrType . '_suffix'           => '%noValue%',
                    $addrType . '_company'          => '%noValue%',
                    $addrType . '_street_address_1' => $this->generate('string', $symNum, $charsType),
                    $addrType . '_street_address_2' => '%noValue%',
                    $addrType . '_region'           => '%noValue%',
                    $addrType . '_city'             => $this->generate('string', $symNum, $charsType),
                    $addrType . '_zip_code'         => $this->generate('string', $symNum, $charsType),
                    $addrType . '_telephone'        => $this->generate('string', $symNum, $charsType),
                    $addrType . '_fax'              => '%noValue%'
                );
            } else {
                return array(
                    $addrType . '_prefix'           => $this->generate('string', $symNum, $charsType),
                    $addrType . '_first_name'       => $this->generate('string', $symNum, $charsType),
                    $addrType . '_middle_name'      => $this->generate('string', $symNum, $charsType),
                    $addrType . '_last_name'        => $this->generate('string', $symNum, $charsType),
                    $addrType . '_suffix'           => $this->generate('string', $symNum, $charsType),
                    $addrType . '_company'          => $this->generate('string', $symNum, $charsType),
                    $addrType . '_street_address_1' => $this->generate('string', $symNum, $charsType),
                    $addrType . '_street_address_2' => $this->generate('string', $symNum, $charsType),
                    $addrType . '_region'           => $this->generate('string', $symNum, $charsType),
                    $addrType . '_city'             => $this->generate('string', $symNum, $charsType),
                    $addrType . '_zip_code'         => $this->generate('string', $symNum, $charsType),
                    $addrType . '_telephone'        => $this->generate('string', $symNum, $charsType),
                    $addrType . '_fax'              => $this->generate('string', $symNum, $charsType)
                );
            }
        }
    }

    /**
     * Creates order for existing cutomer
     * @param bool         $validate         'Submit Order' button will not be pressed
     * @param string       $storeName
     * @param array        $productsToBuy
     * @param array|string $customerEmail
     * @param array        $billForm
     * @param array        $shipForm
     * @param array|string $paymentMethod
     * @param string       $shippingMethod
     * @param array        $reconfigProduct
     * @param array        $giftMessages
     * @param array        $couponCode       Consist of the code and bool for validation
     *                                       (true - should be applied successfully, false - error message should appear)
     *
     * @return bool|integer
     */
    public function createOrderForExistingCustomer($validate = false, $storeName = null,
                                                   $productsToBuy = null, $customerEmail = null,
                                                   $billForm = null, $shipForm = null,
                                                   $paymentMethod = null, $shippingMethod = null,
                                                   $reconfigProduct = null, $giftMessages = null,
                                                   $couponCode = null)
    {
        $this->addParameter('id', '0');
        if ($customerEmail != null) {
                if ($billForm != null) {
                    $this->addParameter('storeName', $storeName);
                    $this->navigateToCreateOrderPage($customerEmail);
                    if ($productsToBuy != null) {
                        $this->addProductsToOrder($productsToBuy);
                    }
                    if ($reconfigProduct != null)
                    {
                        $this->reconfigProduct($reconfigProduct);
                    }
                    if ($giftMessages != null)
                    {
                        $this->addGiftMessage($giftMessages);
                    }
                    if ($couponCode != null)
                    {
                        $rcode = $this->applyCoupon($couponCode);
                        if ($rcode == false)
                        {
                            return false;
                        }
                    }
                    if (is_string($customerEmail)) {
                        $customerEmail = array('email' => $customerEmail);
                    }
                    $this->fillForm($customerEmail, 'order_account_information');
                    if (is_array($billForm)) {
                        $this->fillOrderAddress('exist', 'billing',  $billForm);
                    }
                }
                if ($shipForm != null) {
                    $this->fillOrderAddress('exist', 'shipping', $shipForm);
                } else {
                    $this->fillOrderAddress('sameAsBilling');
                }
                if ($paymentMethod != null) {
                    $this->selectPaymentMethod($paymentMethod);
                }
                if ($shippingMethod != null) {
                    $this->selectShippingMethod($shippingMethod);
                }
                if ($validate == true) {
                    return false;
                } else {
                    $errors = $this->getErrorMessages();
                    $this->assertTrue(empty($errors), $this->messages);
                    $this->clickButton('submit_order', TRUE);
                    $this->addParameter('id', $this->defineIdFromUrl());
                    if ($this->successMessage('success_created_order') == true) {
                        $this->assertTrue($this->successMessage('success_created_order'),
                                $this->messages);
                        return $this->_defineOrderId('view_order');
                    }
                    return false;
                }
        }
    }

    /**
     * Creates order for new cutomer
     *
     * @param bool         $validate 'Submit Order' button will not be pressed
     * @param string       $storeName
     * @param array        $productsToBuy
     * @param array|string $customerEmail
     * @param array        $billForm
     * @param array        $shipForm
     * @param array|string $paymentMethod
     * @param string       $shippingMethod
     * @param array        $reconfigProduct
     * @param array        $giftMessages
     * @param array        $couponCode       Consist of the code and bool for validation
     *                                       ('success' = true - should be applied successfully,
     *                                        'success' = false - error message should appear)
     *
     * @return bool|integer
     */
    public function createOrderForNewCustomer($validate = false, $storeName = null,
                                              $productsToBuy = null, $customerEmail = null,
                                              $billForm = null, $shipForm = null,
                                              $paymentMethod = null, $shippingMethod = null,
                                              $reconfigProduct = null, $giftMessages = null,
                                              $couponCode = null)
    {
        $this->addParameter('id', '0');
        $this->addParameter('storeName', $storeName);
        $this->navigateToCreateOrderPage();
        if ($billForm == null) {
            throw new Exception('You are using method incorrectly. $billForm cannot be null.');
        } else {
            if ($productsToBuy != null) {
                $this->addProductsToOrder($productsToBuy);
            }
            if ($reconfigProduct != null)
            {
                $this->reconfigProduct($reconfigProduct);
            }
            if ($giftMessages != null)
            {
                $this->addGiftMessage($giftMessages);
            }
            if ($couponCode != null)
            {
                $rcode = $this->applyCoupon($couponCode);
                if ($rcode == false)
                {
                    return false;
                }
            }
            if ($customerEmail != null) {
                if (is_string($customerEmail)) {
                    $customerEmail = array('acc_email' => $customerEmail);
                }
                $this->fillForm($customerEmail);
            }
            if (is_array($billForm)) {
                $this->fillOrderAddress('new', 'billing', $billForm);
            }
            if ($shipForm != null) {
                $this->fillOrderAddress('new', 'shipping', $shipForm);
            } else {
                $this->fillOrderAddress('sameAsBilling');
            }
            if ($paymentMethod != null) {
                $this->selectPaymentMethod($paymentMethod);
            }
            if ($shippingMethod != null) {
                $this->selectShippingMethod($shippingMethod);
            }
            if ($validate == true) {
                return false;
            } else {
                $errors = $this->getErrorMessages();
                $this->assertTrue(empty($errors), $this->messages);
                $this->clickButton('submit_order', TRUE);
                $this->addParameter('id', $this->defineIdFromUrl());
                $this->assertTrue($this->checkCurrentPage('view_order'), 'Wrong page is opened');
                if ($this->successMessage('success_created_order') == true) {
                    $this->assertTrue($this->successMessage('success_created_order'),
                            $this->messages);
                    return $this->_defineOrderId('view_order');
                }
                return false;
            }
        }
    }

    /**
     * Fills customer's addresses at the order page.
     *
     * @param string $addressType   'new', 'exist', 'editExist', 'sameAsBilling'
     * @param string $address       'billing' or 'shipping'
     * @param array  $addressData
     */
    public function fillOrderAddress($addressType, $address = null, $addressData = null)
    {
        if (($addressType == 'new') && ($address != null) && ($addressData != null)) {
            $userData = $this->loadData('new_customer_order_' . $address . '_address_reqfields');
            $addrToFill = array_merge($userData, $addressData);
            $this->fillForm($addrToFill);
        }
        if (($addressType == 'exist') && ($address != null) && ($addressData != null))
        {
            if (array_key_exists($address . '_address_choice', $addressData))
            {
                $this->fillForm($addressData);
            } elseif (array_key_exists('first_name', $addressData)) {
                $customer = $addressData['first_name'] . ' ' .
                            $addressData['last_name'] . ', ' .
                            $addressData['street_address_line_1'] . ' ' .
                            $addressData['street_address_line_2'] . ', ' .
                            $addressData['city'] . ', ' .
                            $addressData['zip_code'] . ', ' .
                            $addressData['country'];
                $addrToSearch = array('shipping_same_as_billing_address' => 'no',
                                      'shipping_address_choice'          => $customer);
                $this->fillForm($addrToSearch);
            }
        }
        if (($addressType == 'editExist') && ($address != null) && ($addressData != null))
        {
            if ((array_key_exists($address . '_address_choice', $addressData))
                    && (array_key_exists($address . '_first_name', $addressData)))
            {
                $this->fillForm($addressData);
            }
        }
        if ($addressType == 'sameAsBilling')
        {
            $this->fillForm(array('shipping_same_as_billing_address' => 'yes'));
        }
    }
    /**
     * Creates product needed for creating order.
     *
     * @param string $dataSetName     Dataset name with product information needed for creation.
     *                                Non-empty SKU is obligatory.
     * @param bool $createNewIfLowQty This flag will force creation of the product
     *                                in case when the qty in stock is less than 10.
     *                                The previous product will be deleted.
     */
    public function createProducts($dataSetName, $createNewIfLowQty = FALSE)
    {
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        $this->clickButton('reset_filter', false);
        $this->pleaseWait();
        $productData = $this->loadData($dataSetName);
        if ($createNewIfLowQty == false) {
            if ($this->internalSearch(array('product_sku' => $productData['general_sku'])) == false) {
                $this->productHelper()->createProduct($productData);
                $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
            }
        } else {
            if ($this->internalSearch(array('product_sku' => $productData['general_sku']),
                            'product_grid') == false) {
                $this->productHelper()->createProduct($productData);
                $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
            } else {
                if ($this->internalSearch(array('product_sku'    => $productData['general_sku'],
                                               'product_qty_to' => '10')) == true) {
                    $this->searchAndOpen(array('product_sku' => $productData['general_sku']), TRUE);
                    $this->assertTrue($this->checkCurrentPage('edit_product'),
                            'Wrong page is opened');
                    $this->addParameter('id', $this->defineIdFromUrl());
                    $this->deleteElement('delete', 'confirmation_for_delete');
                    $this->productHelper()->createProduct($productData);
                    $this->assertTrue($this->successMessage('success_saved_product'),
                            $this->messages);
                }
            }
        }
    }

    /**
     * Covering up traces. Able to remove/cancel order and remove customer.
     *
     * @param integer $orderId   Order Id to Cancel
     * @param array $customerEmail   Customer Email that should be removed
     */
    public function coverUpTraces($orderId = null, $customerEmail = null)
    {
        if ($orderId != null) {
            $this->assertTrue($this->_cancelOrder($orderId), 'Could not cancel order');
        }
        if ($customerEmail != null) {
            $this->assertTrue($this->_removeCustomer($customerEmail), 'Could not delete customer');
        }
    }

    /**
     * Cancels orders. Used for covering up traces after testing
     *
     * @param integer $orderId Create Order returns OrderID.
     *                         Pass this parameter from there or anyway you like
     * @return bool  Returns false if the order was not canceled, true if order
     *               was canceled successfully.
     */
    protected function _cancelOrder($orderId)
    {
        $this->assertTrue($this->navigate('manage_sales_orders'),
                'Could not get to Manage Sales Orders page');
        $arg = array(1 => $orderId);
        $this->searchAndChoose($arg, 'sales_order_grid');
        $userData = array('actions' => 'Cancel');
        $this->fillForm($userData, 'sales_order_grid');
        $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
        $this->assertTrue($this->clickButton('submit'), 'Could not press button Submit');
        if ($this->successMessage('success_canceled_order') == true) {
            $this->assertTrue($this->successMessage('success_canceled_order'), $this->messages);
            return true;
        }
        return false;
    }

    /**
     * Deletes customers account. Used for covering up traces after testing
     *
     * @param string $customerEmail  Customer email is unique value in customers account.
     *                               Pass the same parameter as during order for new customer creation
     *                               or customer creation from 'manage_customers' page
     * @return bool                  Returns false if the customer was not deleted,
     *                               true if the customer's account was deleted successfully.
     */
    protected function _removeCustomer($customerEmail)
    {
        $this->assertTrue($this->navigate('manage_customers'));
        $this->assertTrue($this->checkCurrentPage('manage_customers'), 'Wrong page is opened');
        $this->addParameter('id', '0');
        $this->CustomerHelper()->openCustomer($customerEmail);
        $this->deleteElement('delete_customer', 'confirmation_for_delete');
        if ($this->successMessage('success_deleted_customer') == true) {
            $this->assertTrue($this->successMessage('success_deleted_customer'), $this->messages);
            return true;
        }
        return false;
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
            $this->_error = true;
            return FALSE;
        }
    }

    /**
     * Search
     *
     * @param array $data
     * @param string $fieldSetName
     * @return bool
     */
    public function internalSearch(array $data, $fieldSetName = null)
    {
        $this->_prepareDataForSearch($data);
        if (count($data) > 0) {
            if (isset($fieldSetName)) {
                $xpath = $this->getCurrentLocationUimapPage()->
                                findFieldset($fieldSetName)->getXpath();
            } else {
                $xpath = '';
            }
            $totalCount = intval($this->getText($xpath .
                    "//table[@class='actions']//td[@class='pager']//span[@id]"));
            $xpathTR = $xpath . "//table[@class='data']//tr";
            foreach ($data as $key => $value) {
                if (!preg_match('/_from/', $key) and !preg_match('/_to/', $key)) {
                    $xpathTR .= "[contains(.,'$value')]";
                }
            }
            if ($totalCount > 0) {
                $this->fillForm($data);
                $this->clickButton('search', false);
                $this->pleaseWait();
            } elseif ($totalCount == 0) {
                return false;
            }
            if ($this->isElementPresent($xpathTR)) {
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * Orders products during forming order
     * @param array $productData Products in array to add to order.
     */
    public function addProductsToOrder($productData)
    {
        $this->clickButton('add_products', FALSE);
        $this->pleaseWait();
        if (!is_array($productData)) {
            throw new Exception('$productData should be an array.');
        }
        foreach ($productData as $product => $data)
        {
            $xpathProduct = $this->search(array('general_sku' => $productData[$product]['general_sku']));
            $this->assertNotEquals(null, $xpathProduct);
            if (!($this->isElementPresent($xpathProduct . "//a[text()='Configure'][@disabled]"))) {
                $this->searchAndChoose(array('general_sku' => $productData[$product]['general_sku']));
                $this->pleaseWait();
                if (array_key_exists('options', $data))
                {
                    foreach ($data['options'] as $option => $value)
                    {
                        foreach ($value as $clue => $dataset)
                        {
                            if (preg_match('/value/', $clue))
                            {
                                $this->addParameter($option, $dataset);
                            } else {
                                $this->fillForm($dataset);
                            }
                        }
                    }
                }
                $this->clickButton('ok', FALSE);
            }
            $this->searchAndChoose(array('general_sku' => $productData[$product]['general_sku']));
        }
        $this->clickButton('add_selected_products_to_order', FALSE);
        $this->pleaseWait();
    }

    /**
     * The way customer will pay for the order
     *
     * @param array|string $paymentMethodInfo Credit Cards Info (ccn. exp. date, cvv, etc)
     * @param string       $paymentMethod     Payment Method to choose (credit_card is default)
     */
    public function selectPaymentMethod($paymentMethodInfo, $paymentMethod = 'credit_card')
    {
        if (is_string($paymentMethodInfo)) {
            $paymentMethodInfo = $this->loadData($paymentMethodInfo);
        }
        if (!is_array($paymentMethodInfo) && !is_string($paymentMethodInfo)) {
            throw new Exception('Incorrect type of $paymentMethod.');
        }
        $this->pleaseWait();
        $this->clickControl('radiobutton', $paymentMethod, FALSE);
        $this->pleaseWait();
        $this->waitForAjax();
        $this->fillForm($paymentMethodInfo, 'order_payment_method');
    }

    /**
     * The way to ship the order
     *
     * @param string $shippingMethod
     */
    public function selectShippingMethod($shippingMethod)
    {
        if (!is_string($shippingMethod)) {
            throw new Exception('Incorrect type of $shippingMethod.');
        } else {
            $this->clickControl('link', 'get_shipping_methods_and_rates', FALSE);
            $this->pleaseWait();
            $this->addParameter('shipMethod', $shippingMethod);
            $this->clickControl('radiobutton', 'ship_method', FALSE);
            $this->pleaseWait();
        }
    }

    /**
     * Gets to 'Create new Order page'
     *
     * @param array|string $customerData
     */
    public function navigateToCreateOrderPage($customerData = null)
    {
        $this->clickButton('create_new_order', TRUE);
        if ($customerData == null) {
            $this->clickButton('create_new_customer', FALSE);
        } else {
            if (is_string($customerData)) {
                $customerData = array('email' => $customerData);
            }
            $this->assertNotEquals(null, $this->searchAndOpen($customerData['email'], false, 'order_customer_grid'));
        }
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
    public function reconfigProduct($reconfigProduct)
    {
        if (!is_array($reconfigProduct)) {
            throw new Exception('$reconfigProduct should be an array.');
        } else {
            foreach($reconfigProduct as $product => $options)
            {
                $this->addParameter('sku', $options['general_sku']);
                if (array_key_exists('options', $options))
                {
                    $this->fillForm($options['options']);
                }
            }
            $this->clickButton('update_items_and_quantity', FALSE);
            $this->pleaseWait();
        }
    }
    /**
     * Adding gift messaged to products during creating order at the backend.
     *
     * @param array $giftMessages Array with the gift messages for the products
     */
    public function addGiftMessage($giftMessages)
    {
        if (!is_array($giftMessages)) {
            throw new Exception('$giftMessages should be an array.');
        } else {
            foreach($giftMessages as $product => $message)
            {
                $this->addParameter('sku', $message['general_sku']);
                if (array_key_exists('message', $message))
                {
                    $this->clickControl('link', 'gift_options', FALSE);
                    $this->waitForAjax();
                    $this->fillForm($message['message']);
                }
            }
            $this->clickButton('ok', FALSE);
            $this->pleaseWait();
        }
    }
    /**
     * Applying coupon for the products in order
     *
     * @param array $couponCode
     */
    public function applyCoupon($couponCode)
    {
        if (!is_array($couponCode)) {
            throw new Exception('$couponCode should be an array.');
        } else {
            $this->fillForm(array('coupon_code' => $couponCode['coupon_code']));
            $this->clickButton('apply', FALSE);
            $this->pleaseWait();
            if ($couponCode['success'] == 'true')
            {
                $this->assertTrue($this->successMessage('success_applying_coupon'), $this->messages);
                return true;
            } else {
                $this->assertTrue($this->errorMessage('invalid_coupon_code'), $this->messages);
                return false;
            }
        }
    }
}
