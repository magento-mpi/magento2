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

namespace Magento\Checkout\Test\Block\Onepage\Payment;

use Mtf\Block\Block;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Payment\Test\Block\Form;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class Methods
 * One page checkout status
 *
 * @package Magento\Checkout\Test\Block\Onepage\Payment
 */
class Methods extends Block
{
    /**
     * Continue checkout button
     *
     * @var string
     */
    private $continue;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        //Elements
        $this->continue = '#payment-buttons-container button';
    }

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
            /** @var $formBlock \Magento\Payment\Test\Block\Form\Cc */
            $formBlock = new $paymentFormClass($this->_rootElement->find('#payment_form_' . $paymentCode),
                Locator::SELECTOR_CSS);
            $formBlock->fill($fixture);
        }

        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible('.please-wait');
    }
}
