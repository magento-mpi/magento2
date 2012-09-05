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
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_Order_Helper extends Core_Mage_Order_Helper
{
    /**
     * The way customer will pay for the order
     *
     * @param array|string $paymentMethod
     * @param bool $validate
     */
    public function selectPaymentMethod($paymentMethod, $validate = true)
    {
        if (is_string($paymentMethod)) {
            $elements = explode('/', $paymentMethod);
            $fileName = (count($elements) > 1) ? array_shift($elements) : '';
            $paymentMethod = $this->loadDataSet($fileName, implode('/', $elements));
        }
        $payment = (isset($paymentMethod['payment_method'])) ? $paymentMethod['payment_method'] : null;
        $card = (isset($paymentMethod['payment_info'])) ? $paymentMethod['payment_info'] : null;
        if ($this->controlIsPresent('message', 'zero_payment')) {
            return;
        }
        if ($payment) {
            $this->addParameter('paymentTitle', $payment);
            $xpath = $this->_getControlXpath('radiobutton', 'check_payment_method');
            if (!$this->isElementPresent($xpath)) {
                if ($validate) {
                    $this->fail('Payment Method "' . $payment . '" is currently unavailable.');
                }
            } else {
                $this->click($xpath);
                $this->pleaseWait();
                if ($card) {
                    $paymentId = $this->getAttribute($xpath . '/@value');
                    $this->addParameter('paymentId', $paymentId);
                    $this->fillForm($card);
                    $this->validate3dSecure();
                }
            }
        }
    }
}