<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Multishipping\Test\Block\Checkout;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;
use Magento\Multishipping\Test\Fixture\GuestPaypalDirect;

/**
 * Class Shipping
 * Multishipping billing information
 *
 * @package Magento\Multishipping\Test\Block\Checkout
 */
class Billing extends Form
{
    /**
     * Continue checkout button
     *
     * @var string
     */
    protected $continue = '#payment-continue';

    /**
     * Select payment method
     *
     * @param GuestPaypalDirect $fixture
     */
    public function selectPaymentMethod(GuestPaypalDirect $fixture)
    {
        $payment = $fixture->getPaymentMethod();
        $paymentCode = $payment->getPaymentCode();
        $this->_rootElement->find('#p_method_' . $paymentCode, Locator::SELECTOR_CSS)->click();

        $dataConfig = $payment->getDataConfig();
        if (isset($dataConfig['payment_form_class'])) {
            $paymentFormClass = $dataConfig['payment_form_class'];
            /** @var $formBlock \Mtf\Block\Form */
            $formBlock = new $paymentFormClass($this->_rootElement->find('#payment_form_' . $paymentCode,
                Locator::SELECTOR_CSS), $this->mapper);
            $formBlock->fill($fixture);
        }

        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible('.please-wait');
    }
}
