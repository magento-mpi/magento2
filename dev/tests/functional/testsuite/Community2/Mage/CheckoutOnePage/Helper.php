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
 * Helper class Community_Mage_for OnePageCheckout
 *
 * @package     selenium
 * @subpackage  functional_tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_CheckoutOnePage_Helper extends Core_Mage_CheckoutOnePage_Helper
{
    /**
     * Create order using one page checkout
     *
     * @param array|string $checkoutData
     *
     * @return string $orderNumber
     */
    public function frontCreateCheckout($checkoutData)
    {
        if (is_string($checkoutData)) {
            $elements = explode('/', $checkoutData);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $checkoutData = $this->loadDataSet($fileName, implode('/', $elements));
        }
        $this->doOnePageCheckoutSteps($checkoutData);
        $this->frontOrderReview($checkoutData);
        $this->selectTermsAndConditions($checkoutData);
        return $this->submitOnePageCheckoutOrder();
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
                $actualText = trim($this->getText($this->_getControlXpath('pageelement', 'agreement_checkbox_text')));
                $this->assertSame($agreement['agreement_checkbox_text'], $actualText, 'Text is not identical');
            }
            if ($agreement['agreement_content']) {
                $actualText = trim($this->getText($this->_getControlXpath('pageelement', 'agreement_content')));
                $this->assertSame($agreement['agreement_content'], $actualText, 'Text is not identical');
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
        $card = (isset($paymentMethod['payment_info'])) ? $paymentMethod['payment_info'] : null;
        if ($payment) {
            $this->addParameter('paymentTitle', $payment);
            $xpath = $this->_getControlXpath('radiobutton', 'check_payment_method');
            $selectedPayment = $this->_getControlXpath('radiobutton', 'selected_one_payment');
            if ($this->isElementPresent($xpath)) {
                $this->click($xpath);
                if ($card) {
                    $paymentId = $this->getAttribute($xpath . '/@value');
                    $this->addParameter('paymentId', $paymentId);
                    if ($payment == 'Credit Card Direct Post (Authorize.net)') {
                        $this->goToNextOnePageCheckoutStep('payment_method');
                        $this->assertTrue($this->waitForElement($this->_getControlXpath('fieldset', 'andp_frame')),
                            'Frame is not loaded');
                        $this->fillFieldSet($card, 'andp_frame');
                        return;
                    }
                    $this->fillForm($card);
                }
            } elseif (!$this->isElementPresent($selectedPayment)) {
                $this->addVerificationMessage('Payment Method "' . $payment . '" is currently unavailable.');
            }
        }
        $this->goToNextOnePageCheckoutStep('payment_method');
    }
}