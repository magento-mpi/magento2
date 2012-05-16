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
class Core_Mage_CheckoutMultipleAddresses_Helper extends Mage_Selenium_TestCase
{
    /**
     * @staticvar string
     */
    protected static $activeTab = "[contains(@class,'active')]";

    /**
     * @param array $checkout
     *
     * @return array
     */
    public function frontMultipleCheckout(array $checkout)
    {
        $this->doMultipleCheckoutSteps($checkout);
        //Place Order
        return $this->placeMultipleCheckoutOrder();
    }

    /**
     * @param array $checkout
     */
    public function doMultipleCheckoutSteps(array $checkout)
    {
        //Data
        $products = (isset($checkout['products_to_add'])) ? $checkout['products_to_add'] : array();
        $customer = (isset($checkout['checkout_as_customer'])) ? $checkout['checkout_as_customer'] : array();
        $generalCustomerData =
            (isset($checkout['general_customer_data'])) ? $checkout['general_customer_data'] : array();
        $shippingData = (isset($checkout['shipping_data'])) ? $checkout['shipping_data'] : array();
        $paymentData = (isset($checkout['payment_data'])) ? $checkout['payment_data'] : array();
        //Add Product(s)
        foreach ($products as $data) {
            $options = (isset($data['options'])) ? $data['options'] : array();
            $this->productHelper()->frontOpenProduct($data['product_name']);
            $this->productHelper()->frontAddProductToCart($options);
        }
        $this->assertTrue($this->checkCurrentPage('shopping_cart'), $this->getParsedMessages());
        $this->clickControl('link', 'checkout_with_multiple_addresses');
        //If customer not signed in
        $currentPage = $this->getCurrentPage();
        if ($currentPage == 'checkout_multishipping_login'
            || $currentPage == 'checkout_multishipping_login_with_params'
        ) {
            $this->frontSelectCheckoutMethod($customer);
        }
        //If Create an Account
        if ($this->getCurrentPage() == 'checkout_multishipping_register') {
            $this->fillFieldset($generalCustomerData, 'account_info');
            $uimapPage = $this->getUimapPage('frontend', 'checkout_multishipping_addresses');
            $setXpath = $this->_getControlXpath('fieldset', 'checkout_multishipping_form', $uimapPage);
            $this->clickButton('submit', false);
            $this->waitForElement(array($setXpath, $this->_getMessageXpath('general_error'),
                                        $this->_getMessageXpath('general_validation')));
            $this->assertMessageNotPresent('validation');
            $this->validatePage();
        }
        //If customer without address
        if ($this->getCurrentPage() == 'checkout_multishipping_new_shipping') {
            $this->fillFieldset($generalCustomerData, 'create_shipping_address');
            $uimapPage = $this->getUimapPage('frontend', 'checkout_multishipping_addresses');
            $setXpath = $this->_getControlXpath('fieldset', 'checkout_multishipping_form', $uimapPage);
            $this->clickButton('save_address', false);
            $this->waitForElement(array($setXpath, $this->_getMessageXpath('general_error'),
                                        $this->_getMessageXpath('general_validation')));
            $this->validatePage();
        }
        //Select addresses for each product
        $this->selectShippingAddresses($shippingData);
        //Select shipping method for each address
        $this->defineAndSelectShippingMethods($shippingData);
        //Select payment method and billing address
        $this->fillBillingInfo($paymentData);
        $this->frontOrderReview($checkout);
    }

    /**
     * @return array
     */
    public function submitMultipleCheckoutSteps()
    {
        $waitConditions = array($this->_getMessageXpath('success_checkout'), $this->_getMessageXpath('general_error'),
                                $this->_getMessageXpath('general_validation'));
        $this->clickButton('place_order', false);
        $this->waitForElementOrAlert($waitConditions);
        $error = $this->errorMessage();
        $validation = $this->validationMessage();
        if (!$this->checkoutOnePageHelper()->verifyNotPresetAlert() || $error['success'] || $validation['success']) {
            $message = self::messagesToString($this->getMessagesOnPage());
            //@TODO
            //Uncomment and remove workaround for getting fails,
            //not skipping tests if payment methods are inaccessible
            $this->skipTestWithScreenshot($message);
            //$this->fail($message);
        }
        $this->validatePage('checkout_multishipping_success_order');
        $xpath = $this->_getControlXpath('link', 'all_order_number');
        if ($this->isElementPresent($xpath)) {
            $count = $this->getXpathCount($xpath);
            $id = array();
            for ($i = 1; $i <= $count; $i++) {
                $id[] = $this->getText($xpath . '[' . $i . ']');
            }
            return $id;
        }
        return $this->formOrderIdsArray($this->getText("//*[contains(text(),'Your order')]"));
    }

    /**
     * @param string $stepName
     */
    public function assertMultipleCheckoutPageOpened($stepName)
    {
        $uimap = $this->getUimapPage('frontend', 'checkout_multishipping_addresses');
        $setXpath = $this->_getControlXpath('pageelement', $stepName, $uimap);
        if (!$this->isElementPresent($setXpath . self::$activeTab)) {
            $messages = self::messagesToString($this->getMessagesOnPage());
            if ($messages) {
                $this->fail("'" . $stepName . "' step is not selected:\n" . $messages);
            }
            $this->fail("'" . $stepName . "' step is not selected but there is no any message on the page");
        }
    }

    /**
     * @param array $method
     */
    public function frontSelectCheckoutMethod(array $method)
    {
        $checkoutType = (isset($method['checkout_method'])) ? $method['checkout_method'] : '';
        switch ($checkoutType) {
            case 'register':
                $this->clickButton('create_account');
                break;
            case 'login':
                if (isset($method['additional_data'])) {
                    $this->fillFieldset($method['additional_data'], 'log_in_customer');
                }
                //@TODO if wrong login data
                $this->clickButton('login');
                break;
            default:
                break;
        }
    }

    /**
     * @param array $shippingData
     */
    public function selectShippingAddresses(array $shippingData)
    {
        $this->assertMultipleCheckoutPageOpened('select_addresses');
        $this->assertMessageNotPresent('validation');
        $this->assertTrue($this->controlIsPresent('fieldset', 'checkout_multishipping_form'),
            'Ship to Multiple Addresses page is not opened');
        //Define Product(s) qty in order
        $products = array();
        foreach ($shippingData as $oneAddressData) {
            foreach ($oneAddressData['products'] as $product) {
                $name = $product['product_name'];
                $qty = (isset($product['product_qty'])) ? $product['product_qty'] : 1;
                if (isset($products[$name])) {
                    $products[$name] = $products[$name] + $qty;
                } else {
                    $products[$name] = $qty;
                }
            }
        }
        //Verify Product(s) qty in order
        $filledProducts = array();
        foreach ($products as $productName => $qty) {
            $this->addParameter('productName', $productName);
            $isVirtual = false;
            if (!$this->controlIsPresent('dropdown', 'is_any_address_choice')) {
                $productInCard = $this->getAttribute($this->_getControlXpath('field', 'product_qty') . '@value');
                $isVirtual = true;
            } else {
                $productInCard = $this->getXpathCount($this->_getControlXpath('link', 'product'));
            }
            if ($productInCard == 0) {
                $this->fail($productName . ' product is not present in card');
            }
            if ($qty != $productInCard) {
                if (!is_int($qty)) {
                    $this->fillField('product_qty', $qty);
                    $this->clickButton('update_qty_and_addresses');
                    continue;
                }
                if ($isVirtual) {
                    $this->fillField('product_qty', $qty);
                    $this->clickButton('update_qty_and_addresses');
                    $filledProducts[$productName] = 1;
                    continue;
                }
                if ($productInCard > $qty) {
                    while ($productInCard != $qty) {
                        $this->clickControl('link', 'remove_product');
                        $productInCard = $this->getXpathCount($this->_getControlXpath('link', 'product'));
                    }
                } else {
                    $this->fillField('product_qty', $qty - $productInCard + 1);
                    $this->clickButton('update_qty_and_addresses');
                }
            }
            $filledProducts[$productName] = 1;
        }
        $this->assertMessageNotPresent('error', 'shopping_cart_is_empty');
        //Add address if not exist
        $fillData = array();
        foreach ($shippingData as $oneAddressData) {
            $address = (isset($oneAddressData['address'])) ? $oneAddressData['address'] : array();
            $products = (isset($oneAddressData['products'])) ? $oneAddressData['products'] : array();
            if (empty($address)) {
                continue;
            }
            foreach ($products as $product) {
                $this->addParameter('productName', $product['product_name']);
                if (!$this->controlIsPresent('dropdown', 'is_any_address_choice')) {
                    continue;
                }
                $this->addParameter('index', 1);
                $isAddressAdded = $this->orderHelper()->defineAddressToChoose($address, '');
                if (is_null($isAddressAdded)) {
                    $waitConditions = array($this->_getControlXpath('fieldset', 'checkout_multishipping_form'),
                                            $this->_getMessageXpath('general_error'),
                                            $this->_getMessageXpath('general_validation'));
                    $this->clickButton('add_new_address');
                    $this->fillFieldset($address, 'create_shipping_address');
                    $this->clickButton('save_address', false);
                    $this->waitForElement($waitConditions);
                    $this->validatePage();
                    $this->assertMessageNotPresent('validation');
                    $this->assertMessagePresent('success', 'success_saved_address');
                    $isAddressAdded = $this->orderHelper()->defineAddressToChoose($address, '');
                }
                $qty = (isset($product['product_qty'])) ? $product['product_qty'] : 1;
                $arr = array('product' => $product['product_name'],
                             'qty'     => $qty,
                             'address' => $isAddressAdded);
                $fillData[] = $arr;
            }
        }
        //Select shipping address for each product
        foreach ($fillData as $data) {
            $this->addParameter('productName', $data['product']);
            $filledQty = $filledProducts[$data['product']];
            for ($i = $filledQty; $i < $data['qty'] + $filledQty; $i++) {
                $this->addParameter('index', $i);
                $this->fillDropdown('address_choice', $data['address']);
                $filledProducts[$data['product']] = $filledProducts[$data['product']] + 1;
            }
        }
        $this->clickButton('continue_to_shipping_information', false);
        $this->waitForNewPage();
        $this->validatePage();
    }

    /**
     * @param array $shippingData
     */
    public function defineAndSelectShippingMethods(array $shippingData)
    {
        $this->assertMultipleCheckoutPageOpened('shipping_information');

        $actualShippingCount = $this->getXpathCount($this->_getControlXpath('pageelement', 'shipping_methods_forms'));
        $expectedShippingCount = count($shippingData);
        $this->assertEquals($expectedShippingCount, $actualShippingCount,
            'Order should contains ' . $expectedShippingCount . ' shipping addresses but contains '
            . $actualShippingCount);

        //Get actual addresses for shipping methods and Headers
        $headerAddresses = $this->defineAddresses('shipping', $expectedShippingCount);
        foreach ($shippingData as $oneAddressData) {
            $address = (isset($oneAddressData['address'])) ? $oneAddressData['address'] : array();
            $shipping = (isset($oneAddressData['shipping'])) ? $oneAddressData['shipping'] : array();
            $giftOptions = (isset($oneAddressData['gift_options'])) ? $oneAddressData['gift_options'] : array();
            if (empty($address)) {
                continue;
            }
            $header = $this->getAddressId($address, $headerAddresses);
            if (!is_null($header)) {
                $this->addParameter('addressHeader', $header);
                if (!empty($shipping)) {
                    $this->selectShippingMethod($shipping);
                }
                if (!empty($giftOptions)) {
                    $this->addGiftOptions($giftOptions, $header);
                }
            }
        }
        $setXpath = $this->_getControlXpath('pageelement', 'billing_information');
        $this->clickButton('continue_to_billing_information', false);
        $this->waitForElement(array($setXpath . self::$activeTab, $this->_getMessageXpath('general_error'),
                                    $this->_getMessageXpath('general_validation')));
        $this->assertMessageNotPresent('error');
        $this->validatePage('checkout_multishipping_payment_methods');
    }

    /**
     * @param string $addressType
     * @param int $expectedShippingCount
     *
     * @return array
     */
    public function defineAddresses($addressType = 'billing', $expectedShippingCount = 1)
    {
        $headerAddresses = array();
        for ($z = 1; $z <= $expectedShippingCount; $z++) {
            $actualAddress = array();
            $this->addParameter('number', $z);
            if (!$this->controlIsPresent('pageelement', $addressType . '_method_address')) {
                continue;
            }
            $xpath = $this->_getControlXpath('pageelement', $addressType . '_method_address') . '/text()';
            if ($addressType == 'shipping') {
                $header = $this->getText($this->_getControlXpath('pageelement', 'shipping_method_address_header'));
            } else {
                $header = $z;
            }
            $count = $this->getXpathCount($xpath);
            for ($i = 1; $i <= $count; $i++) {
                $text = trim(preg_replace('/^(T:)|(F:)/', '', $this->getText($xpath . '[' . $i . ']')));
                if (!preg_match('/((\w)|(\W))+, ((\w)|(\W))+, ((\w)|(\W))+/', $text)) {
                    $actualAddress[] = $text;
                } else {
                    $text = explode(', ', $text);
                    for ($y = 0; $y < count($text); $y++) {
                        $actualAddress[] = $text[$y];
                    }
                }
            }
            $headerAddresses[$header] = array_diff($actualAddress, array(''));
        }
        return $headerAddresses;
    }

    /**
     * @param array $shipMethod
     */
    public function selectShippingMethod(array $shipMethod)
    {
        $service = (isset($shipMethod['shipping_service'])) ? $shipMethod['shipping_service'] : null;
        $method = (isset($shipMethod['shipping_method'])) ? $shipMethod['shipping_method'] : null;

        if (!$service or !$method) {
            $this->addVerificationMessage('Shipping Service(or Shipping Method) is not set');
        } else {
            $this->addParameter('shipService', $service);
            $this->addParameter('shipMethod', $method);
            $methodUnavailable = $this->_getControlXpath('message', 'ship_method_unavailable');
            $noShipping = $this->_getControlXpath('message', 'no_shipping');
            if ($this->isElementPresent($methodUnavailable) || $this->isElementPresent($noShipping)) {
                //@TODO
                //Remove workaround for getting fails, not skipping tests if shipping methods are not available
                $this->skipTestWithScreenshot('Shipping Service "' . $service . '" is currently unavailable.');
                //$this->addVerificationMessage('Shipping Service "' . $service . '" is currently unavailable.');
            } elseif ($this->isElementPresent($this->_getControlXpath('field', 'ship_service_name'))) {
                $methodXpath = $this->_getControlXpath('radiobutton', 'ship_method');
                $selectedMethod = $this->_getControlXpath('radiobutton', 'one_method_selected');
                if ($this->isElementPresent($methodXpath)) {
                    $this->click($methodXpath);
                    $this->waitForAjax();
                } elseif (!$this->isElementPresent($selectedMethod)) {
                    $this->addVerificationMessage(
                        'Shipping Method "' . $method . '" for "' . $service . '" is currently unavailable');
                }
            } else {
                //@TODO
                //Remove workaround for getting fails, not skipping tests if shipping methods are not available
                $this->skipTestWithScreenshot($service . ': This shipping method is currently not displayed');
                //$this->addVerificationMessage($service . ': This shipping method is currently not displayed');
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * @param array $paymentData
     */
    public function fillBillingInfo(array $paymentData)
    {
        //Data
        $billingAddress = (isset($paymentData['billing_address'])) ? $paymentData['billing_address'] : array();
        $payment = (isset($paymentData['payment'])) ? $paymentData['payment'] : array();
        //Select billing address
        $this->selectBillingAddress($billingAddress);
        //Select payment method
        $this->assertMultipleCheckoutPageOpened('billing_information');
        $this->selectPaymentMethod($payment);
        $setXpath = $this->_getControlXpath('pageelement', 'place_order');
        $this->clickButton('continue_to_review_order', false);
        $this->waitForElement(array($setXpath . self::$activeTab, $this->_getMessageXpath('general_error'),
                                    $this->_getMessageXpath('general_validation')));
        $this->validatePage();
    }

    /**
     * @param array $giftOptions
     * @param string $header
     *
     * @TODO
     */
    public function addGiftOptions(array $giftOptions, $header)
    {
    }

    /**
     * @param array $shippingData
     *
     * @TODO
     */
    public function verifyGiftOptions(array $shippingData)
    {
    }

    /**
     * @param array $paymentMethod
     */
    public function selectPaymentMethod(array $paymentMethod)
    {
        $payment = (isset($paymentMethod['payment_method'])) ? $paymentMethod['payment_method'] : null;
        $card = (isset($paymentMethod['payment_info'])) ? $paymentMethod['payment_info'] : array();
        if ($payment) {
            $this->addParameter('paymentTitle', $payment);
            $xpath = $this->_getControlXpath('radiobutton', 'check_payment_method');
            $selectedPayment = $this->_getControlXpath('radiobutton', 'selected_one_payment');
            if ($this->isElementPresent($xpath)) {
                $this->click($xpath);
            } elseif (!$this->isElementPresent($selectedPayment)) {
                $this->addVerificationMessage('Payment Method "' . $payment . '" is currently unavailable.');
            }
            if ($card) {
                $paymentId = $this->getAttribute($xpath . '/@value');
                $this->addParameter('paymentId', $paymentId);
                $this->fillFieldset($card, 'payment_method');
            }
        }
    }

    /**
     * @param array $address
     */
    public function selectBillingAddress(array $address)
    {
        if (empty($address)) {
            return;
        }
        $actualAddresses = $this->defineAddresses();
        if (is_null($this->getAddressId($address, $actualAddresses))) {
            $this->clickControl('link', 'change_billing_address');
            $additionalAddresses = $this->getXpathCount($this->_getControlXpath('pageelement', 'billing_addresses'));
            $actualAddresses = $this->defineAddresses('billing', $additionalAddresses);
            $param = $this->getAddressId($address, $actualAddresses);
            if (is_null($param)) {
                $this->clickButton('add_new_address');
                $this->fillFieldset($address, 'create_billing_address');
                $this->saveForm('save_address');
                $this->assertMessagePresent('success', 'success_saved_address');
                $additionalAddresses =
                    $this->getXpathCount($this->_getControlXpath('pageelement', 'billing_addresses'));
                $actualAddresses = $this->defineAddresses('billing', $additionalAddresses);
                $param = $this->getAddressId($address, $actualAddresses);
            }
            $this->addParameter('number', $param);
            $this->clickControl('link', 'select_address');
        }
    }

    /**
     * @param array $expectedAddress
     * @param array $actualAddresses
     *
     * @return int|null|string
     */
    public function getAddressId($expectedAddress, $actualAddresses)
    {
        $skipFields = array('set_default_billing_address', 'set_default_shipping_address', 'first_name', 'last_name');
        $needAddress[] = $expectedAddress['first_name'] . ' ' . $expectedAddress['last_name'];
        foreach ($expectedAddress as $key => $value) {
            if (in_array($key, $skipFields)) {
                continue;
            }
            $needAddress[] = $value;
        }
        foreach ($actualAddresses as $headerName => $addressData) {
            $expectedCount = count($addressData);
            $actualCount = 0;
            foreach ($needAddress as $value) {
                if (in_array($value, $addressData)) {
                    $actualCount++;
                }
            }
            if ($expectedCount == $actualCount) {
                return $headerName;
            }
        }
        return null;
    }

    /**
     * Returns order Ids in Array
     *
     * @param string $text
     *
     * @return array
     */
    public function formOrderIdsArray($text)
    {
        $nodes = explode(',', $text);
        $orderIds = array();
        foreach ($nodes as $value) {
            $orderIds[] = preg_replace('/[^0-9]/', '', $value);
        }
        return $orderIds;
    }

    /**
     * @param array $checkout
     */
    public function frontOrderReview(array $checkout)
    {
        $this->assertMultipleCheckoutPageOpened('place_order');
        $this->checkoutOnePageHelper()->frontValidate3dSecure();
        //Data
        $paymentData = (isset($checkout['payment_data'])) ? $checkout['payment_data'] : array();
        $billing = (isset($paymentData['billing_address'])) ? $paymentData['billing_address'] : array();
        $shippings = (isset($checkout['shipping_data'])) ? $checkout['shipping_data'] : array();
        $expectedPayment =
            (isset($paymentData['payment']['payment_method'])) ? $paymentData['payment']['payment_method'] : array();
        $verifyPrices = (isset($checkout['verify_prices'])) ? $checkout['verify_prices'] : array();
        //Verify quantity orders
        $actualQty = $this->getXpathCount($this->_getControlXpath('fieldset', 'shipping_address_info'));
        $this->assertEquals(count($shippings), $actualQty, 'orders quantity is wrong');
        //Verify selected Shipping addresses for orders
        $orderHeaders = array();
        $actualShippings = $this->defineAddresses('shipping', $actualQty);
        foreach ($shippings as $shipping) {
            $address = (isset($shipping['address'])) ? $shipping['address'] : array();
            if (empty($address)) {
                $orderHeaders[] = trim($this->getText("//*[contains(text(), 'Other')]"));
                continue;
            }
            $header = $this->getAddressId($address, $actualShippings);
            if (is_null($header)) {
                $this->addVerificationMessage(
                    'Shipping Address is wrong for one order. [must be : ' . implode(',', $address) . ']');
            }
            $orderHeaders[] = $header;
        }
        //Verify selected Billing address for orders
        if (is_null($this->getAddressId($billing, $this->defineAddresses()))) {
            $this->addVerificationMessage(
                'Billing Address is wrong for orders. [must be : ' . implode(',', $billing) . ']');
        }
        //Verify selected Payment Method for orders
        $actualPayment = trim($this->getText($this->_getControlXpath('pageelement', 'payment_method')));
        if ($actualPayment !== $expectedPayment) {
            $this->addVerificationMessage(
                'Payment Method is wrong. [' . $actualPayment . ' selected, but must be : ' . $expectedPayment . ']');
        }
        $this->assertEmptyVerificationErrors();
        //Get Shipping Addresses Data
        $ordersData = array();
        $i = 1;
        foreach ($orderHeaders as $header) {
            $this->addParameter('addressHeader', $header);
            $ordersData['address_' . $i++] = $this->getOrderDataForAddress();
        }
        if (empty($verifyPrices)) {
            //Remove Prices
            $withoutPrices = array();
            foreach ($ordersData as $k => $addressData) {
                if (isset($addressData['shipping'])) {
                    $shipping = $addressData['shipping'];
                    unset($shipping['price']);
                    $withoutPrices[$k]['shipping'] = $shipping;
                }
                $products = $addressData['products'];
                foreach ($products as $key => $product) {
                    $temp['product_name'] = $product['product_name'];
                    $temp['product_qty'] = $product['product_qty'];
                    $withoutPrices[$k]['products'][$key] = $temp;
                }
            }
            //Verify data without Prices
            $expected = array();
            foreach ($shippings as $key => $shipping) {
                $k = str_replace('_data', '', $key);
                if (isset($shipping['shipping'])) {
                    $expected[$k]['shipping'] = $shipping['shipping'];
                }
                $expected[$k]['products'] = $shipping['products'];
            }
            $this->assertEquals($expected, $withoutPrices);
        } else {
            $ordersData['grand_total'] = $this->getText($this->_getControlXpath('pageelement', 'grand_total'));
            $this->assertEquals($verifyPrices, $ordersData);
        }
    }

    /**
     * @return array
     */
    public function getOrderDataForAddress()
    {
        $addressData = array();
        if ($this->getParameter('addressHeader') != 'Other Items in Your Order') {
            //Get order shipping method data
            $shipping = trim($this->getText($this->_getControlXpath('pageelement', 'shipping_method')));
            list($service, $methodAndPrice) = array_map('trim', explode('-', $shipping));
            list($method, $price) = explode(' ', $methodAndPrice);
            $addressData['shipping']['shipping_service'] = trim($service);
            $addressData['shipping']['shipping_method'] = trim($method);
            $addressData['shipping']['price'] = trim($price);
        }
        //Get order products data
        $products = $this->shoppingCartHelper()->getProductInfoInTable();
        foreach ($products as &$product) {
            $product['product_qty'] = $product['qty'];
            unset($product['qty']);
        }
        $addressData['products'] = $products;
        //Get order total data
        $addressData['total'] = $this->shoppingCartHelper()->getOrderPriceData();
        return $addressData;
    }
}