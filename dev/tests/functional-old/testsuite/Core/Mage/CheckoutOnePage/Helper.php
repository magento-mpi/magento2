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
     * @var string
     */
    protected static $_activeTab = "[contains(@class,'active')]";

    /**
     * @var string
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
        $waitConditions = array(
            $this->_getMessageXpath('success_checkout'),
            $errorMessageXpath,
            $this->_getMessageXpath('general_validation'),
            $this->_getControlXpath('pageelement', 'andp_iframe')
        );
        $this->clickButton('place_order', false);
        $this->waitForElementOrAlert($waitConditions);
        $this->verifyNotPresetAlert();
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

    /**
     * @param array $orderData
     * @return array
     */
    public function formOnePageCheckoutData(array $orderData)
    {
        $products = (isset($orderData['products_to_add'])) ? $orderData['products_to_add'] : array();
        $customer = (isset($orderData['checkout_as_customer'])) ? $orderData['checkout_as_customer'] : array();
        $billing = (isset($orderData['billing_address_data'])) ? $orderData['billing_address_data'] : array();
        $shipping = (isset($orderData['shipping_address_data'])) ? $orderData['shipping_address_data'] : array();
        $shipMethod = (isset($orderData['shipping_data'])) ? $orderData['shipping_data'] : array();
        $payMethod = (isset($orderData['payment_data'])) ? $orderData['payment_data'] : array();
        $checkProd = (isset($orderData['validate_prod_data'])) ? $orderData['validate_prod_data'] : array();
        $checkTotal = (isset($orderData['validate_total_data'])) ? $orderData['validate_total_data'] : array();

        return array($products, $customer, $billing, $shipping, $shipMethod, $payMethod, $checkProd, $checkTotal);
    }

    /**
     * @param array $orderData
     */
    public function doOnePageCheckoutSteps(array $orderData)
    {
        list($products, $customer, $billing, $shipping, $shipMethod, $payMethod) =
            $this->formOnePageCheckoutData($orderData);
        foreach ($products as $data) {
            $this->productHelper()->frontOpenProduct($data['general_name']);
            $this->productHelper()->frontAddProductToCart();
        }
        $this->assertTrue($this->checkCurrentPage('shopping_cart'), $this->getParsedMessages());
        $this->clickButton('proceed_to_checkout');
        if ($this->controlIsVisible('fieldset', 'checkout_method')) {
            $this->frontSelectCheckoutMethod($customer);
        }
        if ($this->frontFillOnePageBillingAddress($billing)) {
            $this->frontFillOnePageShippingAddress($shipping);
        }
        if ($this->controlIsVisible('fieldset', 'shipping_method')) {
            $this->frontSelectShippingMethod($shipMethod);
        }
        $this->frontSelectPaymentMethod($payMethod);
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
        $waitCondition = array(
            $this->_getMessageXpath('general_validation'),
            $this->_getControlXpath('pageelement', 'element_with_class_not_active'),
            $this->getBasicXpathMessagesExcludeCurrent('error')
        );
        $this->clickButton($buttonName, false);
        $this->waitForElementOrAlert($waitCondition);
        $this->assertTrue($this->verifyNotPresetAlert(), $this->getParsedMessages());
        if (!$this->controlIsVisible('pageelement', 'element_with_class_not_active')) {
            $this->assertMessageNotPresent('validation');
        }
        if ($fieldsetName !== 'checkout_method') {
            $this->waitForControlVisible('link', $fieldsetName . '_change');
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
                $this->waitForElement(array(
                    $billingSetXpath . self::$_activeTab,
                    $this->_getMessageXpath('general_error'),
                    $this->_getMessageXpath('general_validation')
                ));
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
     */
    public function frontSelectShippingMethod(array $shipMethod)
    {
        $this->assertOnePageCheckoutTabOpened('shipping_method');
        $giftOptions = (isset($shipMethod['add_gift_options'])) ? $shipMethod['add_gift_options'] : array();
        if (empty($shipMethod)) {
            $this->goToNextOnePageCheckoutStep('shipping_method');
            return;
        }
        if (!isset($shipMethod['shipping_service']) || !isset($shipMethod['shipping_method'])) {
            $this->addVerificationMessage('Shipping Service(or Shipping Method) is not set');
            $this->frontAddGiftOptions($giftOptions);
            $this->goToNextOnePageCheckoutStep('shipping_method');
            return;
        }
        $this->addParameter('shipService', $shipMethod['shipping_service']);
        $this->addParameter('shipMethod', $shipMethod['shipping_method']);
        if ($this->controlIsVisible('message', 'ship_method_unavailable')
            || $this->controlIsVisible('message', 'no_shipping')
        ) {
            $this->skipTestWithScreenshot(
                'Shipping Service "' . $shipMethod['shipping_service'] . '" is currently unavailable.'
            );
        } elseif ($this->controlIsPresent('field', 'ship_service_name')) {
            if ($this->controlIsVisible('radiobutton', 'ship_method')) {
                $this->fillRadiobutton('ship_method', 'Yes');
            } elseif (!$this->controlIsPresent('radiobutton', 'one_method_selected')) {
                $this->addVerificationMessage(
                    'Shipping Method "' . $shipMethod['shipping_method'] . '" for "'
                        . $shipMethod['shipping_service'] . '" is currently unavailable'
                );
            }
        } else {
            $this->skipTestWithScreenshot(
                $shipMethod['shipping_service'] . ': This shipping method is currently not displayed'
            );
        }
        if ($giftOptions) {
            $this->frontAddGiftOptions($giftOptions);
        }
        $this->goToNextOnePageCheckoutStep('shipping_method');
    }

    /**
     * Adding gift message for entire order of each item
     *
     * @param array|string $giftOptions
     */
    public function frontAddGiftOptions(array $giftOptions)
    {
        $this->assertTrue(
            $this->controlIsPresent('checkbox', 'add_gift_options'),
            'You can not add gift option for this order'
        );
        $this->fillCheckbox('add_gift_options', 'Yes');
        if (isset($giftOptions['individual_items'])) {
            foreach ($giftOptions['individual_items'] as $item) {
                $this->_addGiftOptionsForItem($item);
            }
        }
        if (isset($giftOptions['entire_order'])) {
            $this->_addGiftOptionsForOrder($giftOptions['entire_order']);
        }
    }

    /**
     * Add gift options for order
     * @param array $forOrder
     */
    protected function _addGiftOptionsForOrder(array $forOrder)
    {
        if (isset($forOrder['gift_message'])) {
            $this->fillCheckbox('gift_option_for_order', 'Yes');
            if (!$this->isControlExpanded('link', 'add_order_gift_message')) {
                $this->clickControl('link', 'add_order_gift_message', false);
            }
            $this->fillFieldset($forOrder['gift_message'], 'shipping_method');
        }
    }

    /**
     * Add gift options for one product
     *
     * @param array $oneItemData
     */
    protected function _addGiftOptionsForItem(array $oneItemData)
    {
        $this->addParameter('productName', $oneItemData['product_name']);
        if (isset($oneItemData['gift_message'])) {
            $this->fillCheckbox('gift_option_for_item', 'Yes');
            if (!$this->isControlExpanded('link', 'add_item_gift_message')) {
                $this->clickControl('link', 'add_item_gift_message', false);
            }
            $this->fillFieldset($oneItemData['gift_message'], 'shipping_method');
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
        if (!$this->controlIsVisible('fieldset', '3d_secure_card_validation')) {
            return;
        }
        if (!$this->controlIsVisible('pageelement', '3d_secure_iframe')) {
            $this->skipTestWithScreenshot('3D Secure frame is not loaded(maybe wrong card)');
        }
        $this->frame('centinel-authenticate-iframe');
        $this->waitForControl('button', '3d_submit', 10);
        $this->fillField('3d_password', $password);
        $this->clickButton('3d_submit', false);
        if ($this->alertIsPresent()) {
            $this->acceptAlert();
        }
        $this->frame(null);
        try {
            $this->waitForControlNotVisible('fieldset', '3d_secure_card_validation', 30);
        } catch (RuntimeException $e) {
            $this->frame('centinel-authenticate-iframe');
            $this->assertFalse(
                $this->controlIsVisible('pageelement', 'verification_failed'),
                'The card has failed verification with the issuer bank.'
            );
            $this->assertFalse(
                $this->controlIsVisible('pageelement', 'verification_cannot_processed'),
                'Verification cannot be processed'
            );
            $this->assertFalse($this->controlIsVisible('pageelement', 'incorrect_password'), 'Incorrect password');
            if ($this->controlIsVisible('button', '3d_continue')) {
                $this->clickButton('3d_continue', false);
                $this->frame(null);
                $this->waitForControlNotVisible('fieldset', '3d_secure_card_validation', 30);
            }
            $this->frame(null);
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
     * Order review
     *
     * @param array $checkoutData
     */
    public function frontOrderReview(array $checkoutData)
    {
        $this->assertOnePageCheckoutTabOpened('order_review');
        $this->frontValidate3dSecure();
        list($products, , $billing, $shipping, $shipMethod, $payMethod, $checkProd, $checkTotal) =
            $this->formOnePageCheckoutData($checkoutData);

        foreach ($products as $data) {
            $this->addParameter('productName', $data['general_name']);
            if (!$this->controlIsPresent('field', 'product_name')) {
                $this->addVerificationMessage($data['general_name'] . ' product is not in order.');
            }
        }
        if ($billing) {
            $skipBilling = array(
                'billing_address_choice',
                'billing_email',
                'ship_to_this_address',
                'billing_street_address_2',
                'ship_to_different_address',
                'billing_password',
                'billing_confirm_password'
            );
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
            $skipShipping = array(
                'shipping_street_address_2',
                'shipping_address_choice',
                'shipping_save_in_address_book',
                'use_billing_address'
            );
            $this->frontVerifyTypedAddress($shipping, $skipShipping, 'shipping');
        }
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
        if ($payMethod && isset($payMethod['payment_method'])) {
            $actualPayment = $this->getControlAttribute('field', 'payment_method_checkout', 'text');
            if (strcmp($actualPayment, $payMethod['payment_method']) != 0) {
                $this->addVerificationMessage(
                    'Payment method should be: ' . $payMethod['payment_method'] . ' but now ' . $actualPayment
                );
            }
        }
        if ($checkProd && $checkTotal) {
            $this->shoppingCartHelper()->verifyPricesDataOnPage($checkProd, $checkTotal);
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * @param array $address
     * @param array $skipFields
     * @param string $type
     */
    public function frontVerifyTypedAddress($address, $skipFields, $type)
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
        if (array_key_exists($type . '_first_name', $address) && array_key_exists($type . '_last_name', $address)) {
            $address[$type . '_name'] = $address[$type . '_first_name'] . ' ' . $address[$type . '_last_name'];
            $skipFields[] = $type . '_first_name';
            $skipFields[] = $type . '_last_name';
        }
        foreach ($address as $field => $value) {
            if (in_array($field, $skipFields)) {
                continue;
            }
            if (!in_array($value, $actualAddress)) {
                $this->addVerificationMessage("$field with value $value is not shown on the checkout progress bar");
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