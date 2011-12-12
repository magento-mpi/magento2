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
class CheckoutMultipleAddresses_Helper extends Mage_Selenium_TestCase
{
    /**
     * Create order using multiple addresses checkout
     *
     * @param array|string  $checkoutData
     * @return string       $orderNumbers
     */
    public function frontCreateMultipleCheckout($checkoutData)
    {
        if (is_string($checkoutData)) {
            $checkoutData = $this->loadData($checkoutData);
        }
        $checkoutData = $this->arrayEmptyClear($checkoutData);
        $this->doMultipleCheckoutSteps($checkoutData);
        $this->clickButton('place_order', false);
        $this->waitForAjax();
        $this->assertTrue($this->verifyNotPresetAlert(), $this->getParsedMessages());
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->validatePage();
        $xpath = $this->_getControlXpath('link', 'order_number');
        if ($this->isElementPresent($xpath)) {
            return $this->getText($xpath);
        }
        return preg_replace('/[^0-9]/', '', $this->getText("//*[contains(text(),'Your order')]"));
    }

    /**
     * Verifies the alert on the page
     *
     * @return bool
     */
    public function verifyNotPresetAlert()
    {
        if ($this->isAlertPresent()) {
            $text = $this->getAlert();
            $this->getParsedMessages();
            $this->addMessage('error', $text);
            return false;
        }
        return true;
    }

    /**
     * Provides checkout steps
     *
     * @param array  $checkoutData
     */
    public function doMultipleCheckoutSteps($checkoutData)
    {
        $checkoutData = $this->arrayEmptyClear($checkoutData);
        $products   = (isset($checkoutData['products_to_add'])) ? $checkoutData['products_to_add'] : array();
        $customer   = (isset($checkoutData['checkout_as_customer'])) ? $checkoutData['checkout_as_customer'] : NULL;
        $generalShippingAddress = (isset($checkoutData['general_shipping_address'])) ? $checkoutData['general_shipping_address'] : NULL;
        $shipping   = (isset($checkoutData['shipping_address_data'])) ? $checkoutData['shipping_address_data'] : NULL;
        $giftOptions = (isset($checkoutData['gift_options'])) ? $checkoutData['gift_options'] : NULL;
        $shipMethod = (isset($checkoutData['shipping_data'])) ? $checkoutData['shipping_data'] : NULL;
        $billing    = (isset($checkoutData['billing_address_data'])) ? $checkoutData['billing_address_data'] : NULL;
        $payMethod  = (isset($checkoutData['payment_data'])) ? $checkoutData['payment_data'] : NULL;
        if ($products) {
            foreach ($products as $data) {
                $this->productHelper()->frontOpenProduct($data['general_name']);
                if (isset($data['options'])) {
                    $this->productHelper()->frontAddProductToCart($data['options']);
                } else {
                    $this->productHelper()->frontAddProductToCart();
                }
            }
        }
        if ($customer) {
            $this->clickControl('link', 'checkout_with_multiple_addresses');
            $this->frontSelectMultipleCheckoutMethod($customer);
        }
        if ($generalShippingAddress) {
            $this->fillForm($generalShippingAddress);
            if ($customer['checkout_method'] == 'register') {
                $this->clickButton('submit');
            } else {
                $this->clickButton('save_address');
            }
        }
        if ($shipping) {
            foreach ($shipping as $value) {
                if (isset($value['general_name'])) {
                    $this->addParameter('productName', $value['general_name']);
                    $this->frontFillAddress($value['shipping_address'], 'exist');
                } else {
                    $this->frontFillAddress($value['shipping_address'], 'new');
                }
                if (isset($value['qty'])) {
                    $this->fillForm(array('qty' => $value['qty']));
                }
            }
            $this->clickButton('update_qty_and_addresses');
            $this->clickButton('continue_to_shipping_information');
        }
        if ($giftOptions) {
            $this->frontAddGiftMessage($giftOptions);
        }
        if ($shipMethod) {
            foreach ($shipMethod as $key => $value) {
                $this->addParameter('addressHeader', $key);
                if (isset($value['change_shipping_address'])) {
                    $this->clickControl('link', 'change_shipping_address');
                    $this->addParameter('id', $this->defineIdFromUrl());
                    $this->fillForm($value['change_shipping_address']);
                    $this->clickButton('save_address');
                }
                if (isset($value['shipping_method'])) {
                    $this->frontSelectShippingMethod($value['shipping_method']);
                }
            }
            $this->clickButton('continue_to_billing_information');
        }
        if ($billing) {
            $this->selectBillingAddress($billing);
        }
        if ($payMethod) {
            $this->frontSelectPaymentMethod($payMethod);
        }
    }

    /**
     * Selects/Edit/Add new billing address
     *
     * @param array $billing
     * @return void
     */
    public function selectBillingAddress(array $billing)
    {
        $this->clickControl('link', 'change_billing_address');
        foreach ($billing as $key => $value) {
            if (preg_match('/^exist/', $key)) {
                $formXpathString = '';
                foreach ($value as $v) {
                    if ($formXpathString == '' && !is_array($v)) {
                        $formXpathString = 'contains(.,"' . $v . '")';
                    } elseif ($formXpathString != '' && !is_array($v)) {
                        $formXpathString = $formXpathString . ' and contains(.,"' . $v . '")';
                    }
                    $this->addParameter('param', $formXpathString);
                    if (is_array($v)) {
                        $this->clickControl('link', 'edit_address');
                        $this->addParameter('id', $this->defineIdFromUrl());
                        $this->fillForm($v);
                        $this->clickButton('save_address');
                    }
                }
            }
            if (preg_match('/^new/', $key)) {
                $this->clickButton('add_new_address');
                $this->fillForm($value);
                $this->clickButton('save_address');
            }
            if (preg_match('/^select/', $key)) {
                $formXpathString = '';
                foreach ($value as $v) {
                    if ($formXpathString == '') {
                        $formXpathString = 'contains(.,"' . $v . '")';
                    } else {
                        $formXpathString = $formXpathString . ' and contains(.,"' . $v . '")';
                    }
                }
                $this->addParameter('param', $formXpathString);
                $this->clickControl('link', 'select_address');
            }
        }
    }

    /**
     * Select Checkout Method(Multiple Addresses Checkout)
     *
     * @param array $method register|login
     */
    public function frontSelectMultipleCheckoutMethod(array $method)
    {
        $checkoutType = (isset($method['checkout_method'])) ? $method['checkout_method'] : '';
        switch ($checkoutType) {
            case 'register':
                $this->clickButton('create_account');
                break;
            case 'login':
                if (isset($method['additional_data'])) {
                    $this->fillForm($method['additional_data']);
                }
                $this->clickButton('login');
                break;
            default:
                break;
        }
    }

    /**
     * Fills address on frontend
     *
     * @param array $addressData
     * @param string $addressChoice     'new' or 'exist'
     */
    public function frontFillAddress(array $addressData, $addressChoice)
    {
        switch ($addressChoice) {
            case 'new':
                $this->clickButton('add_new_address');
                $this->fillForm($addressData);
                $this->clickButton('save_address');
                break;
            case 'exist':
                $addressLine = $this->orderHelper()->defineAddressToChoose($addressData, 'shipping');
                $this->fillForm(array('shipping_address_choice' => $addressLine));
                break;
            default:
                $this->fail('Incorrect address type');
                break;
        }
    }

    /**
     * The way to ship the order
     *
     * @param array $shipMethod
     *
     */
    public function frontSelectShippingMethod(array $shipMethod)
    {
        $this->messages['error'] = array();
        $service = (isset($shipMethod['shipping_service'])) ? $shipMethod['shipping_service'] : NULL;
        $method = (isset($shipMethod['shipping_method'])) ? $shipMethod['shipping_method'] : NULL;
        if (!$service or !$method) {
            $this->addMessage('error', 'Shipping Service(or Shipping Method) is not set');
        } else {
            $this->addParameter('shipService', $service);
            $this->addParameter('shipMethod', $method);
            $methodUnavailable = $this->_getControlXpath('message', 'ship_method_unavailable');
            $noShipping = $this->_getControlXpath('message', 'no_shipping');
            if ($this->isElementPresent($methodUnavailable) || $this->isElementPresent($noShipping)) {
                $this->addMessage('error', 'No Shipping Method is available for this order');
            } elseif ($this->isElementPresent($this->_getControlXpath('field', 'ship_service_name'))) {
                $methodXpath = $this->_getControlXpath('radiobutton', 'ship_method');
                $selectedMethod = $this->_getControlXpath('radiobutton', 'one_method_selected');
                if ($this->isElementPresent($methodXpath)) {
                    $this->click($methodXpath);
                    $this->waitForAjax();
                } elseif (!$this->isElementPresent($selectedMethod)) {
                    $this->addMessage('error',
                            'Shipping Method "' . $method . '" for "' . $service . '" is currently unavailable');
                }
            } else {
                $this->addMessage('error', 'Shipping Service "' . $service . '" is currently unavailable.');
            }
        }
        if (array_key_exists('add_gift_options', $shipMethod)) {
            $this->frontAddGiftMessage($shipMethod['add_gift_options']);
        }
        $messages = $this->getParsedMessages('error');
        if ($messages) {
            $message = implode("\n", $messages);
            $this->fail($message);
        }

    }

    /**
     * Adding gift message for entire order of each item
     *
     * @param array|string $giftOptions
     *
     */
    public function frontAddGiftMessage(array $giftOptions)
    {
        foreach ($giftOptions as $key => $value) {
            $this->addParameter('addressHeader', $key);
            $this->fillForm(array('add_gift_options' => 'Yes', 'gift_option_for_individual_items' => 'Yes'));
            if (array_key_exists('individual_items', $giftOptions)) {
                foreach ($giftOptions['individual_items'] as $data) {
                    if (isset($data['product_name'])) {
                        $this->addParameter('productName', $data['product_name']);
                        $this->fillForm($data);
                    }
                }
            }
        }
    }

    /**
     * Selecting payment method
     *
     * @param array $paymentMethod
     *
     */
    public function frontSelectPaymentMethod(array $paymentMethod)
    {
        $payment = (isset($paymentMethod['payment_method'])) ? $paymentMethod['payment_method'] : Null;
        $card = (isset($paymentMethod['payment_info'])) ? $paymentMethod['payment_info'] : Null;
        if ($payment) {
            $this->addParameter('paymentTitle', $payment);
            $xpath = $this->_getControlXpath('radiobutton', 'check_payment_method');
            $selectedPayment = $this->_getControlXpath('radiobutton', 'selected_one_payment');
            if ($this->isElementPresent($xpath)) {
                $this->click($xpath);
            } elseif (!$this->isElementPresent($selectedPayment)) {
                $this->fail('Payment Method "' . $payment . '" is currently unavailable.');
            }
            if ($card) {
                $paymentId = $this->getAttribute($xpath . '/@value');
                $this->addParameter('paymentId', $paymentId);
                $this->fillForm($card);
            }
        }
        $this->clickButton('continue_to_review_order');
    }

    /**
     * Enters code to centinel iframe in case it appears.
     * 
     * @param string $password
     * @return void
     */
    public function frontValidate3dSecure($password = '1234')
    {
        $xpathFrame = $this->_getControlXpath('fieldset', '3d_secure_card_validation');
        if ($this->waitForElement($xpathFrame, 5)) {
            $xpath = $this->_getControlXpath('field', '3d_password');
            $xpathContinue = $this->_getControlXpath('button', '3d_continue');
            $xpathSubmit = $this->_getControlXpath('button', '3d_submit');
            $this->waitForElement($xpath);
            if ($this->isElementPresent($xpath)) {
                $this->type($xpath, $password);
                $this->click($xpathSubmit);
                $this->waitForElementNotPresent($xpathSubmit);
                if ($this->waitForElement($xpathContinue, 3)) {
                    $this->click($xpathContinue);
                    $this->waitForElementNotPresent($xpathContinue);
                }
            } else {
                $this->fail('3D Secure frame is not loaded(maybe wrong card)');
            }
        }
    }
}
