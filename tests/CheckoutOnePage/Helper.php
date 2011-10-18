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
     * Create checkout
     *
     * @param array|string  $checkoutData
     * @param bool          $validate           Set to TRUE if no errors should appear during checkout
     *
     */
    public function frontCreateCheckout($checkoutData, $validate = TRUE)
    {
        if (is_string($checkoutData)) {
            $checkoutData = $this->loadData($checkoutData);
        }
        $checkoutData = $this->arrayEmptyClear($checkoutData);
        $products = (isset($checkoutData['products_to_add'])) ? $checkoutData['products_to_add'] : array();
        $customer = (isset($checkoutData['checkout_as_customer'])) ? $checkoutData['checkout_as_customer'] : NULL;
        $billingAddr = (isset($checkoutData['billing_address_data'])) ? $checkoutData['billing_address_data'] : NULL;
        $shippingAddr = (isset($checkoutData['shipping_address_data'])) ? $checkoutData['shipping_address_data'] : NULL;
        $shippingMethod = (isset($checkoutData['shipping_data'])) ? $checkoutData['shipping_data'] : NULL;
        $paymentMethod = (isset($checkoutData['payment_data'])) ? $checkoutData['payment_data'] : NULL;
        $validateProdData = (isset($checkoutData['validate_prod_data'])) ? $checkoutData['validate_prod_data'] : NULL;
        $validateTotalData = (isset($checkoutData['validate_total_data'])) ? $checkoutData['validate_total_data']: NULL;
        if ($products) {
            foreach ($products as $product => $data) {
                $this->productHelper()->frontOpenProduct($data['general_name']);
                $this->productHelper()->frontAddProductToCart($data['general_name']);
            }
            $this->clickButton('proceed_to_checkout');
        }
        if ($customer) {
            $this->frontSelectCheckoutMethod($customer);
            $this->verifyNotPresetAlert($validate);
        }
        if ($billingAddr) {
            $fillShipping = $this->frontFillOnePageBillingAddress($billingAddr);
            $this->verifyNotPresetAlert($validate);
        }
        if ($shippingAddr && $fillShipping) {
            $this->frontFillOnePageShippingAddress($shippingAddr);
            $this->verifyNotPresetAlert($validate);
        }
        if ($shippingMethod) {
            $this->frontSelectShippingMethod($shippingMethod, $validate);
            $this->verifyNotPresetAlert($validate);
        }
        if ($paymentMethod) {
            $this->frontSelectPaymentMethod($paymentMethod, $validate);
            $this->verifyNotPresetAlert($validate);
        }
        if ($validateProdData && $validateTotalData) {
            $this->frontVerifyCheckoutData($validateProdData, $validateTotalData);
        }

        $xpath = $this->_getControlXpath('fieldset', 'order_review') . "[contains(@class,'active')]";
        if ($this->isElementPresent($xpath)) {
            $this->frontOrderReview($checkoutData);
            $this->answerOnNextPrompt('OK');
            $this->clickButton('place_order', FALSE);
            $this->waitForAjax();
            $alert = $this->isAlertPresent();
            if ($alert) {
                $this->fail($this->getAlert());
            }
            $this->waitForPageToLoad();
        } else {
            return FALSE;
        }
    }

    /**
     *
     * @param type $validate
     */
    public function verifyNotPresetAlert($validate = true)
    {
        $alert = $this->isAlertPresent();
        if ($alert && $validate) {
            $text = $this->getAlert();
            $this->fail($text);
        }
    }

    /**
     * Select Checkout Method(Onepage Checkout)
     *
     * @param array $methodName guest|register|login
     */
    public function frontSelectCheckoutMethod($method = 'guest')
    {
        if (is_string($method)) {
            $method = $this->loadData($method);
        }
        $checkoutType = (isset($method['checkout_method'])) ? $method['checkout_method'] : null;
        $page = $this->getCurrentLocationUimapPage();
        $set = $page->findFieldset('checkout_method');
        $xpath = $set->getXpath();

        $this->waitForElement($xpath . "[contains(@class,'active')]");

        switch ($checkoutType) {
            case 'guest':
                $this->fillForm(array('checkout_as_guest' => 'Yes'));
                $this->click($set->findButton('checkout_method_continue'));
                break;
            case 'register':
                $this->fillForm(array('register' => 'Yes'));
                $this->click($set->findButton('checkout_method_continue'));
                break;
            case 'login':
                if (isset($method['additional_data'])) {
                    $this->fillForm($method['additional_data']);
                }
                $billingSetXpath = $page->findFieldset('billing_information')->getXpath();
                $this->click($set->findButton('login'));
                $this->waitForElement(array(self::xpathErrorMessage, self::xpathValidationMessage,
                    $billingSetXpath . "[contains(@class,'active')]"));
                break;
            default:
                $this->click($set->findButton('checkout_method_continue'));
                break;
        }
    }

    /**
     * The way to ship the order
     *
     * @param array|string $shippingMethod
     * @param bool         $validate
     *
     */
    public function frontSelectShippingMethod($shippingMethod, $validate = TRUE)
    {
        $setXpath = $this->_getControlXpath('fieldset', 'shipping_method') . "[contains(@class,'active')]";
        $this->waitForElement($setXpath);

        if (is_string($shippingMethod)) {
            $shippingMethod = $this->loadData($shippingMethod);
        }
        $this->messages['error'] = array();
        $shipService = (isset($shippingMethod['shipping_service'])) ? $shippingMethod['shipping_service'] : NULL;
        $shipMethod = (isset($shippingMethod['shipping_method'])) ? $shippingMethod['shipping_method'] : NULL;
        if (!$shipService or !$shipMethod) {
            $this->messages['error'][] = 'Shipping Service(or Shipping Method) is not set';
        } else {
            $this->addParameter('shipService', $shipService);
            $this->addParameter('shipMethod', $shipMethod);
            $methodUnavailable = $this->_getControlXpath('message', 'ship_method_unavailable');
            $noShipping = $this->_getControlXpath('message', 'no_shipping');
            if ($this->isElementPresent($methodUnavailable) || $this->isElementPresent($noShipping)) {
                $this->messages['error'][] = 'No Shipping Method is available for this order';
            } elseif ($this->isElementPresent($this->_getControlXpath('field', 'ship_service_name'))) {
                $method = $this->_getControlXpath('radiobutton', 'ship_method');
                $selectedMethod = $this->_getControlXpath('radiobutton', 'one_method_selected');
                if ($this->isElementPresent($method)) {
                    $this->click($method);
                    $this->waitForAjax();
                } elseif (!$this->isElementPresent($selectedMethod)) {
                    $this->messages['error'][] = 'Shipping Method "' . $shipMethod . '" for "'
                            . $shipService . '" is currently unavailable.';
                }
            } else {
                $this->messages['error'][] = 'Shipping Service "' . $shipService . '" is currently unavailable.';
            }
        }
        if ($this->messages['error'] && $validate) {
            $this->fail(implode("\n", $this->messages['error']));
        }
        if (array_key_exists('add_gift_options', $shippingMethod)) {
            $this->frontAddGiftMessage($shippingMethod['add_gift_options']);
        }
        $this->clickButton('ship_method_continue', FALSE);
    }

    /**
     * Adding gift message for entire order of each item
     *
     * @param array|string $giftOptions
     *
     */
    public function frontAddGiftMessage($giftOptions)
    {
        if (is_string($giftOptions)) {
            $giftOptions = $this->loadData($giftOptions);
        }
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
     * @param bool  $validate
     *
     */
    public function frontSelectPaymentMethod($paymentMethod, $validate = TRUE)
    {
        $setXpath = $this->_getControlXpath('fieldset', 'payment_method') . "[contains(@class,'active')]";
        $this->waitForElement($setXpath);
        if (is_string($paymentMethod)) {
            $paymentMethod = $this->loadData($paymentMethod);
        }
        $payment = (isset($paymentMethod['payment_method'])) ? $paymentMethod['payment_method'] : NULL;
        $card = (isset($paymentMethod['payment_info'])) ? $paymentMethod['payment_info'] : NULL;
        if ($payment) {
            $this->addParameter('paymentTitle', $payment);
            $xpath = $this->_getControlXpath('radiobutton', 'check_payment_method');
            $selectedPayment = $this->_getControlXpath('radiobutton', 'selected_one_payment');
            if ($this->isElementPresent($xpath)) {
                $this->click($xpath);
            } elseif (!$this->isElementPresent($selectedPayment) && $validate) {
                $this->fail('Payment Method "' . $payment . '" is currently unavailable.');
            }
            if ($card) {
                $paymentId = $this->getAttribute($xpath . '/@value');
                $this->addParameter('paymentId', $paymentId);
                $this->fillForm($card, 'order_payment_method');
            }
            $this->clickButton('payment_method_continue', FALSE);
            $this->frontValidate3dSecure();
        }
    }

    /**
     * Enters code to centinel iframe in case it appears.
     */
    public function frontValidate3dSecure($password = '1234')
    {
        $xpath = $this->_getControlXpath('fieldset', 'payment_method') . "[contains(@class,'active')]";
        $this->waitForElementNotPresent($xpath);
        $xpath = $this->_getControlXpath('fieldset', '3d_secure_card_validation');
        if ($this->isElementPresent($xpath)) {
            $alert = $this->isAlertPresent();
            if ($alert) {
                $text = $this->getAlert();
                $this->fail($text);
            }
            $xpath = $this->_getControlXpath('field', '3d_password');
            $this->waitForElement($xpath);
            if ($this->isElementPresent($xpath)) {
                $this->type($xpath, $password);
                $this->clickButton('3d_submit', FALSE);
                $xpathContinue = $this->_getControlXpath('button', '3d_continue');
                $this->waitForElement($xpathContinue);
                if ($this->isElementPresent($xpathContinue)) {
                    $this->clickButton('3d_continue', FALSE);
                    $this->waitForElementNotPresent($xpathContinue);
                }
            } else {
                $this->fail('wrong card');
            }

//            $xpath = $this->_getControlXpath('field', '3d_password');
//            $this->waitForElement($xpath);
//            $this->type($xpath, $password);
//            $this->clickButton('3d_submit', FALSE);
//            $this->waitForElementNotPresent($xpath);
//            $xpathContinue = $this->_getControlXpath('button', '3d_continue');
//            $this->waitForElement($xpathContinue);
//            if ($this->isElementPresent($xpathContinue)) {
//                $this->clickButton('3d_continue', FALSE);
//            }
//            $this->pleaseWait();
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
                $this->fillForm(array($addressType . '_address_choice' => 'label=' . $addressLine));
                break;
            default:
                $this->fail('error');
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
        $setXpath = $this->_getControlXpath('fieldset', $addressType . '_information') . "[contains(@class,'active')]";
        $this->waitForElement($setXpath);
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
     * @param array|string $addressData
     * @return bool        $fillShipping
     */
    public function frontFillOnePageBillingAddress($addressData)
    {
        if (is_string($addressData)) {
            $addressData = $this->loadData($addressData);
        }
        $this->frontFillOnePageAddress($addressData, 'billing');
        $xpath = $this->_getControlXpath('radiobutton', 'ship_to_this_address');
        if ($this->isElementPresent($xpath)) {
            $fillShipping = (!$this->verifyChecked($xpath)) ? TRUE : FALSE;
        } else {
            $fillShipping = FALSE;
        }
        $this->clickButton('billing_continue', false);
        return $fillShipping;
    }

    /**
     * Fills onepage shipping address
     *
     * @param array|string $addressData
     * @return bool
     */
    public function frontFillOnePageShippingAddress($addressData)
    {
        if (is_string($addressData)) {
            $addressData = $this->loadData($addressData);
        }
        $this->frontFillOnePageAddress($addressData, 'shipping');
        $this->clickButton('shipping_continue', false);
    }

    /**
     * Order review
     *
     * @param array|string  $checkoutData
     *
     */
    public function frontOrderReview($checkoutData)
    {
        if (is_string($checkoutData)) {
            $checkoutData = $this->loadData($checkoutData);
        }
        $checkoutData = $this->arrayEmptyClear($checkoutData);
        $products = (isset($checkoutData['products_to_add'])) ? $checkoutData['products_to_add'] : array();
        $billingAddr = (isset($checkoutData['billing_address_data'])) ? $checkoutData['billing_address_data'] : NULL;
        $shippingAddr = (isset($checkoutData['shipping_address_data'])) ? $checkoutData['shipping_address_data'] : NULL;
        $shippingMethod = (isset($checkoutData['shipping_data'])) ? $checkoutData['shipping_data'] : NULL;
        $paymentMethod = (isset($checkoutData['payment_data'])) ? $checkoutData['payment_data'] : NULL;
        if ($products) {
            foreach ($products as $product => $data) {
                $this->addParameter('productName', $data['general_name']);
                $xpathProduct = $this->_getControlXpath('field', 'product_name');
                $this->assertTrue($this->isElementPresent($xpathProduct), $data . ' product is not in order.');
            }
        }
        if ($billingAddr) {
            if (array_key_exists('billing_address_choice', $billingAddr)) {
                unset($billingAddr['billing_address_choice']);
            }
            if (array_key_exists('billing_email', $billingAddr)) {
                unset($billingAddr['billing_email']);
            }
            if (array_key_exists('ship_to_this_address', $billingAddr)) {
                unset($billingAddr['ship_to_this_address']);
            }
            if (array_key_exists('ship_to_different_address', $billingAddr)) {
                unset($billingAddr['ship_to_different_address']);
            }
            if (array_key_exists('billing_password', $billingAddr)) {
                unset($billingAddr['billing_password']);
            }
            if (array_key_exists('billing_confirm_password', $billingAddr)) {
                unset($billingAddr['billing_confirm_password']);
            }
            $xpathChange = $this->_getControlXpath('link', 'billing_address_change_link');
            $this->waitForElement($xpathChange);
            foreach ($billingAddr as $field => $data) {
                $this->addParameter('billingParameter', $data);
                $xpathBilling = $this->_getControlXpath('field', 'billing_address_checkout');
                $this->assertTrue($this->isElementPresent($xpathBilling),
                        'Billing ' . $data . ' is not shown on the checkout progress bar');
            }
        }
        if ($shippingAddr) {
            if (array_key_exists('shipping_address_choice', $shippingAddr)) {
                unset($shippingAddr['shipping_address_choice']);
            }
            if (array_key_exists('shipping_save_in_address_book', $shippingAddr)) {
                unset($shippingAddr['shipping_save_in_address_book']);
            }
            if (array_key_exists('use_billing_address', $shippingAddr)) {
                unset($shippingAddr['use_billing_address']);
            }
            $xpathChange = $this->_getControlXpath('link', 'shipping_address_change_link');
            $this->waitForElement($xpathChange);
            foreach ($shippingAddr as $field => $data) {
                $this->addParameter('shippingParameter', $data);
                $xpathShipping = $this->_getControlXpath('field', 'shipping_address_checkout');
                $this->assertTrue($this->isElementPresent($xpathShipping),
                        'Shipping ' . $data . ' is not shown on the checkout progress bar');
            }
        }
        if ($shippingMethod) {
            if (array_key_exists('shipping_service', $shippingMethod) &&
                    array_key_exists('shipping_method', $shippingMethod)) {
                $xpathChange = $this->_getControlXpath('link', 'shipping_method_change_link');
                $this->waitForElement($xpathChange);
                $this->addParameter('shippingMethod',
                        $shippingMethod['shipping_service'] . ' - ' . $shippingMethod['shipping_method']);
                $xpathShipMethod = $this->_getControlXpath('field', 'shipping_method_checkout');
                $this->assertTrue($this->isElementPresent($xpathShipMethod),
                        'Shipping Method is not shown on the checkout progress bar.');
            }
        }
        if ($paymentMethod) {
            $xpathChange = $this->_getControlXpath('link', 'payment_method_change_link');
            $this->waitForElement($xpathChange);
            if (array_key_exists('payment_method', $paymentMethod)) {
                $this->addParameter('paymentMethod', $paymentMethod['payment_method']);
                $xpathPayMethod = $this->_getControlXpath('field', 'payment_method_checkout');
                $this->assertTrue($this->isElementPresent($xpathPayMethod),
                        'Payment method is not shown on the checkout progress bar.');
            }

            if (isset($paymentMethod['payment_info'])) {
                $this->addParameter('paymentCardType', $paymentMethod['payment_info']['card_type']);
                $xpathPayCardType = $this->_getControlXpath('field', 'payment_info_card_type_checkout');
                $this->assertTrue($this->isElementPresent($xpathPayCardType),
                        'Payment card type is not shown on the checkout progress bar.');
                $this->addParameter('paymentCardNumber', substr($paymentMethod['payment_info']['card_number'], -4));
                $xpathPayCardNumber = $this->_getControlXpath('field', 'payment_info_card_number_checkout');
                $this->assertTrue($this->isElementPresent($xpathPayCardNumber),
                        'Payment card number (last 4 digits) is not shown on the checkout progress bar.');
                if (array_key_exists('name_on_card', $paymentMethod)) {
                    $this->addParameter('paymentCardName', $paymentMethod['payment_info']['name_on_card']);
                    $xpathPayCardName = $this->_getControlXpath('field', 'payment_info_card_name_checkout');
                    $this->assertTrue($this->isElementPresent($xpathPayCardName),
                            'Name on the payment card is not shown on the checkout progress bar.');
                }
            }
        }
    }

    /**
     * Verify checkout prices info
     *
     * @param string|array $productData
     * @param string|array $orderPriceData
     */
    public function frontVerifyCheckoutData($productData, $orderPriceData)
    {
        $this->shoppingCartHelper()->verifyPricesDataOnPage($productData, $orderPriceData);
    }

}
