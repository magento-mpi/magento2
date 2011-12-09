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
class CheckoutOnePage_Helper extends Mage_Selenium_TestCase
{

    /**
     *
     * @var string
     */
    protected static $activeTab = "[contains(@class,'active')]";

    /**
     *
     * @var string
     */
    protected static $notActiveTab = "[not(contains(@class,'active'))]";

    /**
     * Create order using one page checkout
     *
     * @param array|string  $checkoutData
     * @return string       $orderNumber
     */
    public function frontCreateCheckout($checkoutData)
    {
        if (is_string($checkoutData)) {
            $checkoutData = $this->loadData($checkoutData);
        }
        $this->doOnePageCheckoutSteps($checkoutData);
        $this->frontOrderReview($checkoutData);
        $this->clickButton('place_order', false);
        $this->waitForAjax();
        $this->assertTrue($this->verifyNotPresetAlert(), $this->getParsedMessages());
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->validatePage('onepage_checkout_success');

        $xpath = $this->_getControlXpath('link', 'order_number');
        if ($this->isElementPresent($xpath)) {
            return $this->getText($xpath);
        }

        return preg_replace('/[^0-9]/', '', $this->getText("//*[contains(text(),'Your order')]"));
    }

    /**
     *
     * @param array  $checkoutData
     */
    public function doOnePageCheckoutSteps($checkoutData)
    {
        $checkoutData = $this->arrayEmptyClear($checkoutData);
        $products   = (isset($checkoutData['products_to_add'])) ? $checkoutData['products_to_add'] : array();
        $customer   = (isset($checkoutData['checkout_as_customer'])) ? $checkoutData['checkout_as_customer'] : NULL;
        $billing    = (isset($checkoutData['billing_address_data'])) ? $checkoutData['billing_address_data'] : NULL;
        $shipping   = (isset($checkoutData['shipping_address_data'])) ? $checkoutData['shipping_address_data'] : NULL;
        $shipMethod = (isset($checkoutData['shipping_data'])) ? $checkoutData['shipping_data'] : NULL;
        $payMethod  = (isset($checkoutData['payment_data'])) ? $checkoutData['payment_data'] : NULL;

        if ($products) {
            foreach ($products as $product => $data) {
                $this->productHelper()->frontOpenProduct($data['general_name']);
                $this->productHelper()->frontAddProductToCart();
            }
            $this->clickButton('proceed_to_checkout');
        }
        if ($customer) {
            $this->frontSelectCheckoutMethod($customer);
        }
        if ($billing) {
            $fillShipping = $this->frontFillOnePageBillingAddress($billing);
        }
        if ($shipping && $fillShipping) {
            $this->frontFillOnePageShippingAddress($shipping);
        }
        if ($shipMethod) {
            $this->frontSelectShippingMethod($shipMethod);
        }
        if ($payMethod) {
            $this->frontSelectPaymentMethod($payMethod);
        }
    }

    /**
     *
     */
    public function verifyNotPresetAlert()
    {
        if ($this->isAlertPresent()) {
            $text = $this->getAlert();
            $this->_parseMessages();
            $this->addVerificationMessage($text);
            return false;
        }
        return true;
    }

    /**
     *
     * @param type $fieldsetName
     */
    public function assertOnePageCheckoutTabOpened($fieldsetName)
    {
        $setXpath = $this->_getControlXpath('fieldset', $fieldsetName);
        if (!$this->isElementPresent($setXpath . self::$activeTab)) {
            $messages = $this->getMessagesOnPage();
            if ($messages && is_array($messages)) {
                $messages = implode("\n", call_user_func_array('array_merge', $messages));
            }
            $this->fail("'" . $fieldsetName . "' step is not selected:\n" . $messages);
        }
    }

    /**
     *
     * @param type $fieldsetName
     */
    public function goToNextOnePageCheckoutStep($fieldsetName)
    {
        $setXpath = $this->_getControlXpath('fieldset', $fieldsetName);
        $buttonName = $fieldsetName . '_continue';
        $this->clickButton($buttonName, false);
        $this->waitForAjax();
        $this->assertTrue($this->verifyNotPresetAlert(), $this->getParsedMessages());
        $this->waitForElement(array(self::$xpathErrorMessage,
                                    self::$xpathValidationMessage,
                                    $setXpath . self::$notActiveTab));
    }

    /**
     * Select Checkout Method(Onepage Checkout)
     *
     * @param array $methodName guest|register|login
     */
    public function frontSelectCheckoutMethod(array $method)
    {
        $this->assertOnePageCheckoutTabOpened('checkout_method');

        $checkoutType = (isset($method['checkout_method'])) ? $method['checkout_method'] : '';

        switch ($checkoutType) {
            case 'guest':
                $this->fillForm(array('checkout_as_guest' => 'Yes'));
                $this->goToNextOnePageCheckoutStep('checkout_method');
                break;
            case 'register':
                $this->fillForm(array('register' => 'Yes'));
                $this->goToNextOnePageCheckoutStep('checkout_method');
                break;
            case 'login':
                if (isset($method['additional_data'])) {
                    $this->fillForm($method['additional_data']);
                }
                $billingSetXpath = $this->_getControlXpath('fieldset', 'billing_information');
                $this->clickButton('login', false);
                $this->waitForElement(array(self::$xpathErrorMessage,
                                            self::$xpathValidationMessage,
                                            $billingSetXpath . self::$activeTab));
                break;
            default:
                $this->goToNextOnePageCheckoutStep('checkout_method');
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
        $this->assertOnePageCheckoutTabOpened('shipping_method');

        $service = (isset($shipMethod['shipping_service'])) ? $shipMethod['shipping_service'] : NULL;
        $method = (isset($shipMethod['shipping_method'])) ? $shipMethod['shipping_method'] : NULL;

        if (!$service or !$method) {
            $this->addVerificationMessage('Shipping Service(or Shipping Method) is not set');
        } else {
            $this->addParameter('shipService', $service);
            $this->addParameter('shipMethod', $method);
            $methodUnavailable = $this->_getControlXpath('message', 'ship_method_unavailable');
            $noShipping = $this->_getControlXpath('message', 'no_shipping');
            if ($this->isElementPresent($methodUnavailable) || $this->isElementPresent($noShipping)) {
                $this->addVerificationMessage('No Shipping Method is available for this order');
            } elseif ($this->isElementPresent($this->_getControlXpath('field', 'ship_service_name'))) {
                $methodXpath = $this->_getControlXpath('radiobutton', 'ship_method');
                $selectedMethod = $this->_getControlXpath('radiobutton', 'one_method_selected');
                if ($this->isElementPresent($methodXpath)) {
                    $this->click($methodXpath);
                    $this->waitForAjax();
                } elseif (!$this->isElementPresent($selectedMethod)) {
                    $this->addVerificationMessage('Shipping Method "' . $method . '" for "'
                            . $service . '" is currently unavailable');
                }
            } else {
                $this->addVerificationMessage('Shipping Service "' . $service . '" is currently unavailable.');
            }
        }

        if (array_key_exists('add_gift_options', $shipMethod)) {
            $this->frontAddGiftMessage($shipMethod['add_gift_options']);
        }

        $messages = $this->getParsedMessages('verificationErrors');
        if ($messages) {
            $message = implode("\n", $messages);
            $this->fail($message);
        }

        $this->goToNextOnePageCheckoutStep('shipping_method');
    }

    /**
     * Adding gift message for entire order of each item
     *
     * @param array|string $giftOptions
     *
     */
    public function frontAddGiftMessage(array $giftOptions)
    {
        if (array_key_exists('entire_order', $giftOptions)) {
            $this->fillForm($giftOptions['entire_order']);
        }
        if (array_key_exists('individual_items', $giftOptions)) {
            $this->fillForm(array('gift_option_for_individual_items' => 'Yes'));
            foreach ($giftOptions['individual_items'] as $clue => $dataset) {
                if (isset($dataset['product_name'])) {
                    $this->addParameter('productName', $dataset['product_name']);
                    $this->fillForm($dataset);
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
        $this->assertOnePageCheckoutTabOpened('payment_method');

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
        $this->goToNextOnePageCheckoutStep('payment_method');
    }

    /**
     * Enters code to centinel iframe in case it appears.
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

    /**
     * Fills address on frontend
     *
     * @param array $addressData
     * @param string $addressChoise     'New Address' or 'exist'
     * @param type $addressType         'billing' or 'shipping'
     */
    public function frontFillAddress(array $addressData, $addressChoise, $addressType)
    {
        switch ($addressChoise) {
            case 'New Address':
                $xpath = $this->_getControlXpath('dropdown', $addressType . '_address_choice');
                if (!$this->isElementPresent($xpath)) {
                    unset($addressData[$addressType . '_address_choice']);
                }
                $this->fillForm($addressData);
                break;
            case 'exist':
                $addressLine = $this->orderHelper()->defineAddressToChoose($addressData, $addressType);
                $this->fillForm(array($addressType . '_address_choice' => $addressLine));
                break;
            default:
                $this->fail('Incorrect ' . $addressType . ' address type');
                break;
        }
    }

    /**
     * Fills onepage address
     *
     * @param array $addressData
     * @param string $addressType   'billing' or 'shipping'
     */
    public function frontFillOnePageAddress(array $addressData, $addressType)
    {
        $checkoutMethodXpath = $this->_getControlXpath('fieldset', 'checkout_method');
        if ($this->isElementPresent($checkoutMethodXpath)) {
            $checkoutMethod = 'guest_or_register';
        } else {
            $checkoutMethod = 'login';
        }
        $addressChoise = (isset($addressData[$addressType . '_address_choice']))
                            ? $addressData[$addressType . '_address_choice']
                            : 'exist';
        if ($checkoutMethod == 'guest_or_register' && $addressChoise == 'exist') {
            $this->fail('Cannot choose existing address for guest');
        }
        $this->frontFillAddress($addressData, $addressChoise, $addressType);
    }

    /**
     * Fills onepage billing address
     *
     * @param array $addressData
     * @return bool        $fillShipping
     */
    public function frontFillOnePageBillingAddress(array $addressData)
    {
        $this->assertOnePageCheckoutTabOpened('billing_information');
        $this->frontFillOnePageAddress($addressData, 'billing');
        $xpath = $this->_getControlXpath('radiobutton', 'ship_to_this_address');
        if ($this->isElementPresent($xpath) && !$this->verifyChecked($xpath)) {
            $fillShipping = TRUE;
        } else {
            $fillShipping = FALSE;
        }
        $this->goToNextOnePageCheckoutStep('billing_information');

        return $fillShipping;
    }

    /**
     * Fills onepage shipping address
     *
     * @param array $addressData
     * @return bool
     */
    public function frontFillOnePageShippingAddress(array $addressData)
    {
        $this->assertOnePageCheckoutTabOpened('shipping_information');
        $this->frontFillOnePageAddress($addressData, 'shipping');
        $this->goToNextOnePageCheckoutStep('shipping_information');
    }

    /**
     * Order review
     *
     * @param array  $checkoutData
     *
     */
    public function frontOrderReview(array $checkoutData)
    {
        $this->assertOnePageCheckoutTabOpened('order_review');
        $this->frontValidate3dSecure();

        $checkoutData = $this->arrayEmptyClear($checkoutData);
        $products   = (isset($checkoutData['products_to_add'])) ? $checkoutData['products_to_add'] : array();
        $billing    = (isset($checkoutData['billing_address_data'])) ? $checkoutData['billing_address_data'] : NULL;
        $shipping   = (isset($checkoutData['shipping_address_data'])) ? $checkoutData['shipping_address_data'] : NULL;
        $shipMethod = (isset($checkoutData['shipping_data'])) ? $checkoutData['shipping_data'] : NULL;
        $payMethod  = (isset($checkoutData['payment_data'])) ? $checkoutData['payment_data'] : NULL;
        $checkProd  = (isset($checkoutData['validate_prod_data'])) ? $checkoutData['validate_prod_data'] : NULL;
        $checkTotal = (isset($checkoutData['validate_total_data'])) ? $checkoutData['validate_total_data'] : NULL;

        if ($products) {
            foreach ($products as $product => $data) {
                $name = $data['general_name'];
                $this->addParameter('productName', $name);
                $xpathProduct = $this->_getControlXpath('field', 'product_name');
                if (!$this->isElementPresent($xpathProduct)) {
                    $this->addVerificationMessage($name . ' product is not in order.');
                }
            }
        }

        if ($billing) {
            $skipBilling = array('billing_address_choice', 'billing_email', 'ship_to_this_address',
                'ship_to_different_address', 'billing_password',
                'billing_confirm_password', 'billing_street_address_2');

            if (isset($shipping['use_billing_address']) && $shipping['use_billing_address'] == 'Yes') {
                foreach ($billing as $key => $value) {
                    if (!in_array($key, $skipBilling)) {
                        $shipping[preg_replace('/^billing_/', 'shipping_', $key)] = $value;
                    }
                }
            }
            $this->frontVerifyTypedAddress($billing, $skipBilling, 'billing');
        }

        if ($shipping) {
            $skipShipping = array('shipping_address_choice', 'shipping_save_in_address_book',
                'use_billing_address', 'shipping_street_address_2');

            $this->frontVerifyTypedAddress($shipping, $skipShipping, 'shipping');
        }

        if ($shipMethod && isset($shipMethod['shipping_service']) && isset($shipMethod['shipping_method'])) {
            $xpathShipMethod = $this->_getControlXpath('field', 'shipping_method_checkout');
            $text = $this->getText($xpathShipMethod);
            $price = $this->getText($xpathShipMethod . '/span');
            $text = trim(preg_replace('/' . preg_quote($price) . '/', '', $text));
            $text = trim(preg_replace('/\(\w+\. Tax \$[0-9\.]+\)/', '', $text));
            $expectedMethod = $shipMethod['shipping_service'] . ' - ' . $shipMethod['shipping_method'];
            if (strcmp($expectedMethod, $text) != 0) {
                $this->addVerificationMessage('Shipping method should be: ' . $expectedMethod . ' but now ' . $text);
            }
        }

        if ($payMethod && isset($payMethod['payment_method'])) {
            $xpathPayMethod = $this->_getControlXpath('field', 'payment_method_checkout');
            if ($this->isElementPresent($xpathPayMethod . '/p/strong')) {
                $xpathPayMethod .='/p/strong';
            }
            $text = $this->getText($xpathPayMethod);
            if (strcmp($text, $payMethod['payment_method']) != 0) {
                $this->addVerificationMessage('Payment method should be: ' . $payMethod['payment_method']
                        . ' but now ' . $text);
            }
        }

        $this->shoppingCartHelper()->verifyPricesDataOnPage($checkProd, $checkTotal);

        if ($this->getParsedMessages('verificationErrors')) {
            $this->fail(implode("\n", call_user_func_array('array_merge', $this->getParsedMessages())));
        }
    }

    /**
     *
     * @param type $addressData
     * @param type $skipFields
     * @param type $type
     */
    public function frontVerifyTypedAddress($addressData, $skipFields, $type)
    {
        $text = $this->getText($this->_getControlXpath('field', $type . '_address_checkout'));
        $text = preg_replace('/((\r)?\n)|(, )/', '\n', preg_replace('/(T:)|(F:)/', '', $text));
        $text = explode('\n', $text);
        $text = array_map('trim', $text);
        if (array_key_exists($type . '_first_name', $addressData)
                && array_key_exists($type . '_last_name', $addressData)) {
            $addressData[$type . '_name'] = $addressData[$type . '_first_name']
                    . ' ' . $addressData[$type . '_last_name'];
            $skipFields[] = $type . '_first_name';
            $skipFields[] = $type . '_last_name';
        }

        foreach ($addressData as $field => $value) {
            if (in_array($field, $skipFields)) {
                continue;
            }
            if (!in_array($value, $text)) {
                $this->addVerificationMessage($field . ' with value ' . $value
                        . ' is not shown on the checkout progress bar');
            }
        }
    }

}
