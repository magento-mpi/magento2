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

namespace Magento\Checkout\Test\Block\Multishipping;

use Mtf\Block\Block;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Payment\Test\Block\Form;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class Shipping
 * Multishipping billing information
 *
 * @package Magento\Checkout\Test\Block\Multishipping
 */
class Billing extends Block
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
     * @param Checkout $fixture
     */
    public function selectPaymentMethod(Checkout $fixture)
    {
        $payment = $fixture->getPaymentMethod();
        $paymentCode = $payment->getPaymentCode();
        $this->_rootElement->find('#p_method_' . $paymentCode, Locator::SELECTOR_CSS)->click();

        $dataConfig = $payment->getDataConfig();
        if (isset($dataConfig['payment_form_class'])) {
            $paymentFormClass = $dataConfig['payment_form_class'];
            /** @var $formBlock \Mtf\Block\Form */
            $formBlock = new $paymentFormClass($this->_rootElement->find('#payment_form_' . $paymentCode),
                Locator::SELECTOR_CSS);
            $formBlock->fill($fixture);
        }

        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible('.please-wait');
    }
}
