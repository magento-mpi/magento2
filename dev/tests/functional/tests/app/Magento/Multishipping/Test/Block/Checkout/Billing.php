<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Multishipping\Test\Block\Checkout;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;

/**
 * Class Shipping
 * Multishipping billing information
 *
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
     * @param array $payment
     * @return void
     */
    public function selectPaymentMethod(array $payment)
    {
        $this->_rootElement->find('#p_method_' . $payment['method'], Locator::SELECTOR_CSS)->click();
        $dataConfig = $payment['dataConfig'];
        if (isset($dataConfig['payment_form_class'])) {
            /** @var $formBlock \Mtf\Block\Form */
            $formBlock = $this->blockFactory->create(
                $dataConfig['payment_form_class'],
                ['element' => $this->_rootElement->find('#payment_form_' . $payment['method'], Locator::SELECTOR_CSS)]
            );
            $formBlock->fill($payment['credit_card']);
        }

        $this->_rootElement->find($this->continue, Locator::SELECTOR_CSS)->click();
        $this->waitForElementNotVisible('.please-wait');
    }
}
