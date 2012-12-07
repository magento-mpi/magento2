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
class Enterprise2_Mage_Order_Helper extends Core_Mage_Order_Helper
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
        if (is_null($payment)) {
            return;
        }
        $this->addParameter('paymentTitle', $payment);
        if ($this->controlIsPresent('radiobutton', 'check_payment_method')) {
            $this->fillRadiobutton('check_payment_method', 'Yes');
            $this->pleaseWait();
        } elseif (!$this->controlIsPresent('pageelement', 'selected_one_payment')) {
            if ($validate) {
                $this->fail('Payment Method "' . $payment . '" is currently unavailable.');
            }
            return;
        }
        if ($card) {
            $paymentId = $this->getControlAttribute('radiobutton', 'check_payment_method', 'value');
            $this->addParameter('paymentId', $paymentId);
            $this->fillFieldset($card, 'order_payment_method');
            $this->validate3dSecure();
        }
    }
}