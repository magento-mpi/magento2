<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CheckoutOnePage
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class Core_Mage_for OnePageCheckout
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_CheckoutOnePage_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * @staticvar string
     */
    protected static $_activeTab = "[contains(@class,'active')]";

    /**
     * @staticvar string
     */
    protected static $_notActiveTab = "[not(contains(@class,'active'))]";

    /**
     * Create order using one page checkout
     *
     * @param array|string $checkoutData
     *
     * @return string $orderNumber
     */
    public function frontCreateCheckout($checkoutData)
    {
        $checkoutData = $this->fixtureDataToArray($checkoutData);
        $this->doOnePageCheckoutSteps($checkoutData);
        $this->frontOrderReview($checkoutData);
        $this->selectTermsAndConditions($checkoutData);
        return $this->submitOnePageCheckoutOrder();
    }

    /**
     * @return string
     */
    public function submitOnePageCheckoutOrder()
    {
        $errorMessageXpath = $this->getBasicXpathMessagesExcludeCurrent('error');
        $waitConditions = array($this->_getMessageXpath('success_checkout'), $errorMessageXpath,
                                $this->_getMessageXpath('general_validation'),
                                $this->_getControlXpath('pageelement', 'andp_iframe'));
        $this->clickButton('place_order', false);
        $this->waitForElementOrAlert($waitConditions);
        $this->verifyNotPresetAlert();
        //@TODO Remove workaround for getting fails, not skipping tests if payment methods are inaccessible
        $this->paypalHelper()->verifyMagentoPayPalErrors();
        $this->assertMessageNotPresent('error');
        $this->assertEmptyVerificationErrors();
        if ($this->controlIsVisible('pageelement', 'andp_iframe')) {
            $this->frame('directpost-iframe');
            $message = $this->getElement("//table//td")->text();
            $this->frame(null);
            $this->fail($message);
        }
        $this->validatePage('onepage_checkout_success');
        if ($this->controlIsPresent('link', 'order_number')) {
            return $this->getControlAttribute('link', 'order_number', 'text');
        }

        return preg_replace('/[^0-9]/', '', $this->getControlAttribute('message', 'success_checkout_guest', 'text'));
    }

    /*
     * @param array $checkoutData
     * @return array
     */
    protected function _onePageStepsVars($checkoutData)
    {
        $onePageStepsData = array();
        $onePageStepsData['products'] =
            (isset($checkoutData['products_to_add'])) ? $checkoutData['products_to_add'] : array();
        $onePageStepsData['customer'] =
            (isset($checkoutData['checkout_as_customer'])) ? $checkoutData['checkout_as_customer'] : array();
        $onePageStepsData['billing'] =
            (isset($checkoutData['billing_address_data'])) ? $checkoutData['billing_address_data'] : array();
        $onePageStepsData['shipping'] =
            (isset($checkoutData['shipping_address_data'])) ? $checkoutData['shipping_address_data'] : array();
        $onePageStepsData['shipMethod'] =
            (isset($checkoutData['shipping_data'])) ? $checkoutData['shipping_data'] : array();
        $onePageStepsData['payMethod'] =
            (isset($checkoutData['payment_data'])) ? $checkoutData['payment_data'] : array();
        return $onePageStepsData;
    }

    /**
     * @param array $checkoutData
     */
    public function doOnePageCheckoutSteps($checkoutData)
    {
        $onePageStepsData = $this->_onePageStepsVars($checkoutData);

        foreach ($onePageStepsData['products'] as $data) {
            $this->productHelper()->frontOpenProduct($data['general_name']);
            $this->productHelper()->frontAddProductToCart();
        }
        $this->assertTrue($this->checkCurrentPage('shopping_cart'), $this->getParsedMessages());
        $this->clickButton('proceed_to_checkout');
        if ($this->controlIsPresent('fieldset', 'checkout_method')) {
            $this->frontSelectCheckoutMethod($onePageStepsData['customer']);
        }
        $fillShipping = $this->frontFillOnePageBillingAddress($onePageStepsData['billing']);
        if ($fillShipping) {
            $this->frontFillOnePageShippingAddress($onePageStepsData['shipping']);
        }
        if ($this->controlIsPresent('fieldset', 'shipping_method')) {
            $this->frontSelectShippingMethod($onePageStepsData['shipMethod']);
        }
        $this->frontSelectPaymentMethod($onePageStepsData['payMethod']);
    }

    /**
     * @return bool
     */
    public function verifyNotPresetAlert()
    {
        if ($this->alertIsPresent()) {
            $text = $this->alertText();
            $this->acceptAlert();
            $this->_parseMessages();
            $this->addVerificationMessage($text);
            return false;
        }
        return true;
    }

    /**
     * @param string $fieldsetName
     */
    public function assertOnePageCheckoutTabOpened($fieldsetName)
    {
        $this->addParameter('elementXpath', $this->_getControlXpath('fieldset', $fieldsetName));
        if (!$this->controlIsPresent('pageelement', 'element_with_class_active')) {
            $this->fail("'" . $fieldsetName . "' step is not selected but there is no any message on the page");
        }
    }

    /**
     * @param string $fieldsetName
     */
    public function goToNextOnePageCheckoutStep($fieldsetName)
    {
        $buttonName = $fieldsetName . '_continue';
        $this->addParameter('elementXpath', $this->_getControlXpath('fieldset', $fieldsetName));
        $waitCondition = array($this->_getMessageXpath('general_validation'),
                               $this->_getControlXpath('pageelement', 'element_with_class_not_active'),
                               $this->getBasicXpathMessagesExcludeCurrent('error'));
        $this->clickButton($buttonName, false);
        $this->waitForElementOrAlert($waitCondition);
        $this->assertTrue($this->verifyNotPresetAlert(), $this->getMessagesOnPage());
        if (!$this->controlIsPresent('pageelement', 'element_with_class_not_active')) {
            $this->assertMessageNotPresent('error');
            $this->assertMessageNotPresent('validation');
        }
        if ($fieldsetName !== 'checkout_method') {
            $this->waitForElement($this->_getControlXpath('link', $fieldsetName . '_change'));
        }
    }

    /**
     * Select Checkout Method(Onepage Checkout)
     *
     * @param array $method
     */
    public function frontSelectCheckoutMethod(array $method)
    {
        $this->assertOnePageCheckoutTabOpened('checkout_method');
        $checkoutType = (isset($method['checkout_method'])) ? $method['checkout_method'] : '';

        switch ($checkoutType) {
            case 'guest':
                $this->fillCheckbox('checkout_as_guest', 'Yes');
                $this->goToNextOnePageCheckoutStep('checkout_method');
                break;
            case 'register':
                $this->fillCheckbox('register', 'Yes');
                $this->goToNextOnePageCheckoutStep('checkout_method');
                break;
            case 'login':
                if (isset($method['additional_data'])) {
                    $this->fillForm($method['additional_data']);
                }
                $billingSetXpath = $this->_getControlXpath('fieldset', 'billing_information');
                $this->clickButton('login', false);
                $this->waitForElement(array($billingSetXpath . self::$_activeTab,
                                            $this->_getMessageXpath('general_error'),
                                            $this->_getMessageXpath('general_validation')));
                break;
            default:
                $this->goToNextOnePageCheckoutStep('checkout_method');
                break;
        }
    }

    /**
     * @param @method
     * @param @service
     */
    protected function _shipsUnavailable($method, $service)
    {
        if ($this->controlIsPresent('radiobutton', 'ship_method')) {
            $this->fillRadiobutton('ship_method', 'Yes');
        } elseif (!$this->controlIsPresent('radiobutton', 'one_method_selected')) {
            $this->addVerificationMessage(
                'Shipping Method "' . $method . '" for "' . $service . '" is currently unavailable');
        }
    }

    /**
     * @param $shipMethod
     */
    protected function _shipFormAndGiftMessage($shipMethod)
    {
        if (array_key_exists('add_gift_options', $shipMethod)) {
            $this->fillForm($shipMethod['add_gift_options']);
            $this->frontAddGiftMessage($shipMethod['add_gift_options']);
        }
    }

    /**
     * The way to ship the order
     *
     * @param array $shipMethod
     */
    public function frontSelectShippingMethod(array $shipMethod)
    {
        $this->assertOnePageCheckoutTabOpened('shipping_method');
        if ($shipMethod) {
            $service = (isset($shipMethod['shipping_service'])) ? $shipMethod['shipping_service'] : null;
            $method = (isset($shipMethod['shipping_method'])) ? $shipMethod['shipping_method'] : null;

            if (!$service or !$method) {
                $this->addVerificationMessage('Shipping Service(or Shipping Method) is not set');
            } else {
                $this->addParameter('shipService', $service);
                $this->addParameter('shipMethod', $method);
                if ($this->controlIsPresent('message', 'ship_method_unavailable')
                    || $this->controlIsPresent('message', 'no_shipping')
                ) {
                    //@TODO
                    //Remove workaround for getting fails, not skipping tests if shipping methods are not available
                    $this->skipTestWithScreenshot('Shipping Service "' . $service . '" is currently unavailable.');
                    //$this->addVerificationMessage('Shipping Service "' . $service . '" is currently unavailable.');
                } elseif ($this->controlIsPresent('field', 'ship_service_name')) {
                    $this->_shipsUnavailable($method, $service);
                } else {
                    //@TODO
                    //Remove workaround for getting fails, not skipping tests if shipping methods are not available
                    $this->skipTestWithScreenshot($service . ': This shipping method is currently not displayed');
                    //$this->addVerificationMessage($service . ': This shipping method is currently not displayed');
                }
            }
            $this->_shipFormAndGiftMessage($shipMethod);
        }
        $this->goToNextOnePageCheckoutStep('shipping_method');
    }

    /**
     * Adding gift message for entire order of each item
     *
     * @param array|string $giftOptions
     */
    public function frontAddGiftMessage(array $giftOptions)
    {
        if (array_key_exists('entire_order', $giftOptions)) {
            $this->fillForm($giftOptions['entire_order']);
        }
        if (array_key_exists('individual_items', $giftOptions)) {
            $this->fillCheckbox('gift_option_for_individual_items', 'Yes');
            foreach ($giftOptions['individual_items'] as $dataset) {
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
     */
    public function frontSelectPaymentMethod(array $paymentMethod)
    {
        $this->assertOnePageCheckoutTabOpened('payment_method');
        $payment = (isset($paymentMethod['payment_method'])) ? $paymentMethod['payment_method'] : null;
        if (!$this->controlIsPresent('message', 'zero_payment') || $payment != 'No Payment Information Required') {
            $this->checkoutMultipleAddressesHelper()->selectPaymentMethod($paymentMethod);
        }
        $this->goToNextOnePageCheckoutStep('payment_method');
        if (isset($paymentMethod['payment_info']) && ($this->getParameter('paymentId') === 'authorizenet_directpost')) {
            $this->fillFieldset($paymentMethod['payment_info'], 'andp_frame');
        }
    }

    /**
     * Enters code to centinel iframe in case it appears.
     *
     * @param string $password
     */
    public function frontValidate3dSecure($password = '1234')
    {
        $this->addParameter('elementXpath', $this->_getControlXpath('fieldset', '3d_secure_card_validation'));
        if ($this->controlIsPresent('pageelement', 'element_not_disabled_style')) {
            $waitCondition = array($this->_getControlXpath('button', '3d_continue'),
                                   $this->_getControlXpath('pageelement', 'incorrect_password'),
                                   $this->_getControlXpath('pageelement', 'element_with_disabled_style'));
            if (!$this->controlIsVisible('pageelement', '3d_secure_iframe')) {
                //Skipping test, but not failing
                $this->skipTestWithScreenshot('3D Secure frame is not loaded(maybe wrong card)');
                //$this->fail('3D Secure frame is not loaded(maybe wrong card)');
            }
            $this->frame('centinel-authenticate-iframe');
            $this->waitForElement($this->_getControlXpath('button', '3d_submit'), 10);
            $this->fillField('3d_password', $password);
            $this->clickButton('3d_submit', false);
            $this->frame(null);
            $this->waitForElement($waitCondition);
            if ($this->controlIsPresent('button', '3d_continue')) {
                $this->clickButton('3d_continue', false);
                $this->waitForElement($this->_getControlXpath('pageelement', 'element_with_disabled_style'));
            }
        }
    }

    /**
     * Fills address on frontend
     *
     * @param array $addressData
     * @param string $addressChoice 'New Address' or 'exist'
     * @param string $addressType 'billing' or 'shipping'
     */
    public function frontFillAddress(array $addressData, $addressChoice, $addressType)
    {
        switch ($addressChoice) {
            case 'New Address':
                if (!$this->controlIsPresent('dropdown', $addressType . '_address_choice')) {
                    unset($addressData[$addressType . '_address_choice']);
                }
                $this->fillForm($addressData);
                break;
            case 'exist':
                $addressLine = $this->orderHelper()->defineAddressToChoose($addressData, $addressType);
                $this->fillDropdown($addressType . '_address_choice', $addressLine);
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
     * @param string $addressType 'billing' or 'shipping'
     */
    public function frontFillOnePageAddress(array $addressData, $addressType)
    {
        if ($addressData) {
            if ($this->controlIsPresent('fieldset', 'checkout_method')) {
                $checkoutMethod = 'guest_or_register';
            } else {
                $checkoutMethod = 'login';
            }
            $addressChoice = (isset($addressData[$addressType . '_address_choice']))
                ? $addressData[$addressType . '_address_choice']
                : 'exist';
            if ($checkoutMethod == 'guest_or_register' && $addressChoice == 'exist') {
                $this->fail('Cannot choose existing address for guest');
            }
            $this->frontFillAddress($addressData, $addressChoice, $addressType);
        }
    }

    /**
     * Fills onepage billing address
     *
     * @param array $addressData
     *
     * @return bool $fillShipping
     */
    public function frontFillOnePageBillingAddress(array $addressData)
    {
        $this->assertOnePageCheckoutTabOpened('billing_information');
        $this->frontFillOnePageAddress($addressData, 'billing');
        if ($this->controlIsPresent('radiobutton', 'ship_to_different_address')) {
            $fillShipping = $this->getControlAttribute('radiobutton', 'ship_to_different_address', 'selectedValue');
        } else {
            $fillShipping = false;
        }
        $this->goToNextOnePageCheckoutStep('billing_information');

        return $fillShipping;
    }

    /**
     * Fills onepage shipping address
     *
     * @param array $addressData
     *
     * @return bool
     */
    public function frontFillOnePageShippingAddress(array $addressData)
    {
        $this->assertOnePageCheckoutTabOpened('shipping_information');
        $this->frontFillOnePageAddress($addressData, 'shipping');
        $this->goToNextOnePageCheckoutStep('shipping_information');
    }

    /**
     * Checks product in Order
     * @param $products
     */
    protected function _productsCheck($products)
    {
        foreach ($products as $data) {
            $name = $data['general_name'];
            $this->addParameter('productName', $name);
            if (!$this->controlIsPresent('field', 'product_name')) {
                $this->addVerificationMessage($name . ' product is not in order.');
            }
        }
    }

    /**
     * @param $billing
     * @param $shipping
     */
    protected function _billingShipping($billing, $shipping)
    {
        if ($billing) {
            $skipBilling =
                array('billing_address_choice', 'billing_email', 'ship_to_this_address', 'billing_street_address_2',
                      'ship_to_different_address', 'billing_password', 'billing_confirm_password');
            if (isset($shipping['use_billing_address']) && $shipping['use_billing_address'] == 'Yes') {
                foreach ($billing as $key => $value) {
                    if (!in_array($key, $skipBilling)) {
                        $shipping[preg_replace('/^billing_/', 'shipping_', $key)] = $value;
                    }
                }
            }
            $this->frontVerifyTypedAddress($billing, $skipBilling, 'billing');
        }
    }

    /**
     * @param shipping
     */
    protected function _shipping($shipping)
    {
        if ($shipping) {
            $skipShipping =
                array('shipping_street_address_2', 'shipping_address_choice', 'shipping_save_in_address_book',
                      'use_billing_address');
            $this->frontVerifyTypedAddress($shipping, $skipShipping, 'shipping');
        }
    }

    /**
     * @param $shipMethod
     */
    protected function _shipMethod($shipMethod)
    {
        if ($shipMethod && isset($shipMethod['shipping_service']) && isset($shipMethod['shipping_method'])) {
            $text = $this->getControlAttribute('field', 'shipping_method_checkout', 'text');
            $price = $this->getControlAttribute('field', 'shipping_method_checkout_price', 'text');
            $text = trim(preg_replace('/' . preg_quote($price) . '/', '', $text));
            $text = trim(preg_replace('/\(\w+\. Tax \$[0-9\.]+\)/', '', $text));
            $expectedMethod = $shipMethod['shipping_service'] . ' - ' . $shipMethod['shipping_method'];
            if (strcmp($expectedMethod, $text) != 0) {
                $this->addVerificationMessage('Shipping method should be: ' . $expectedMethod . ' but now ' . $text);
            }
        }
    }

    /**
     * @param $payMethod
     */
    protected function _payMethod($payMethod)
    {
        if ($payMethod && isset($payMethod['payment_method'])) {
            if ($this->controlIsPresent('field', 'payment_method_checkout_credit_card')) {
                $text = $this->getControlAttribute('field', 'payment_method_checkout_credit_card', 'text');
            } else {
                $text = $this->getControlAttribute('field', 'payment_method_checkout', 'text');
            }
            if (strcmp($text, $payMethod['payment_method']) != 0) {
                $this->addVerificationMessage(
                    'Payment method should be: ' . $payMethod['payment_method'] . ' but now ' . $text);
            }
        }
    }

    /**
     * @param $checkProd
     * @param $checkTotal
     */
    protected function _checkProdAndTotal($checkProd, $checkTotal)
    {
        if ($checkProd && $checkTotal) {
            $this->shoppingCartHelper()->verifyPricesDataOnPage($checkProd, $checkTotal);
        }
    }

    /**
     * Order review
     *
     * @param array $checkoutData
     */
    public function frontOrderReview(array $checkoutData)
    {
        $this->assertOnePageCheckoutTabOpened('order_review');
        $this->frontValidate3dSecure();

        $products = (isset($checkoutData['products_to_add'])) ? $checkoutData['products_to_add'] : array();
        $billing = (isset($checkoutData['billing_address_data'])) ? $checkoutData['billing_address_data'] : array();
        $shipping = (isset($checkoutData['shipping_address_data'])) ? $checkoutData['shipping_address_data'] : array();
        $shipMethod = (isset($checkoutData['shipping_data'])) ? $checkoutData['shipping_data'] : array();
        $payMethod = (isset($checkoutData['payment_data'])) ? $checkoutData['payment_data'] : array();
        $checkProd = (isset($checkoutData['validate_prod_data'])) ? $checkoutData['validate_prod_data'] : array();
        $checkTotal = (isset($checkoutData['validate_total_data'])) ? $checkoutData['validate_total_data'] : array();

        $this->_productsCheck($products);
        $this->_billingShipping($billing, $shipping);
        $this->_shipping($shipping);
        $this->_shipMethod($shipMethod);
        $this->_payMethod($payMethod);
        $this->_checkProdAndTotal($checkProd, $checkTotal);
        $this->assertEmptyVerificationErrors();
    }

    /**
     * @param array $addressData
     * @param array $skipFields
     * @param string $type
     */
    public function frontVerifyTypedAddress($addressData, $skipFields, $type)
    {
        $addressText = $this->getControlAttribute('field', $type . '_address_checkout', 'text');
        $addressText = explode("\n", $addressText);
        $actualAddress = array();
        foreach ($addressText as $addressLine) {
            $addressLine = trim(preg_replace('/^(T:)|(F:)|(VAT:)/', '', $addressLine));
            if (!preg_match('/((\w)|(\W))+, ((\w)|(\W))+, ((\w)|(\W))+/', $addressLine)) {
                $actualAddress[] = $addressLine;
            } else {
                $text = explode(', ', $addressLine);
                for ($y = 0; $y < count($text); $y++) {
                    $actualAddress[] = $text[$y];
                }
            }
        }
        if (array_key_exists($type . '_first_name', $addressData)
            && array_key_exists($type . '_last_name', $addressData)
        ) {
            /*@TODO remove comments after fix MAGETWO-8894
            $addressData[$type . '_name'] =
                $addressData[$type . '_first_name'] . ' ' . $addressData[$type . '_last_name'];*/
            $skipFields[] = $type . '_first_name';
            $skipFields[] = $type . '_last_name';
        }

        foreach ($addressData as $field => $value) {
            if (in_array($field, $skipFields)) {
                continue;
            }
            if (!in_array($value, $actualAddress)) {
                $this->addVerificationMessage(
                    $field . ' with value ' . $value . ' is not shown on the checkout progress bar');
            }
        }
    }

    /**
     * @param array $checkoutData
     */
    public function selectTermsAndConditions(array $checkoutData)
    {
        $agreements = (isset($checkoutData['agreement'])) ? $checkoutData['agreement'] : array();
        foreach ($agreements as $agreement) {
            $this->markTestIncomplete('MAGETWO-9011');
            $id = isset($agreement['agreement_id']) ? $agreement['agreement_id'] : null;
            $this->addParameter('termsId', $id);
            $this->fillCheckbox('agreement_select', $agreement['agreement_select']);
            if ($agreement['agreement_checkbox_text']) {
                $actualText = $this->getControlAttribute('pageelement', 'agreement_checkbox_text', 'text');
                $this->assertSame($agreement['agreement_checkbox_text'], $actualText, 'Text is not identical');
            }
            if ($agreement['agreement_content']) {
                $actualText = $this->getControlAttribute('pageelement', 'agreement_content', 'text');
                $this->assertSame($agreement['agreement_content'], $actualText, 'Text is not identical');
            }
        }
    }
}