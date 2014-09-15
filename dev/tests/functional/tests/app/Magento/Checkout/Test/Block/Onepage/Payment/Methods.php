<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Block\Onepage\Payment;

use Mtf\Block\Form;

/**
 * Class Methods
 * One page checkout status payment method block
 *
 */
class Methods extends Form
{
    /**
     * Payment method selector
     *
     * @var string
     */
    protected $paymentMethod = '[for=p_method_%s]';

    /**
     * Continue checkout button
     *
     * @var string
     */
    protected $continue = '#payment-buttons-container button';

    /**
     * Wait element
     *
     * @var string
     */
    protected $waitElement = '.loading-mask';

    /**
     * Purchase order number selector
     *
     * @var string
     */
    protected $purchaseOrderNumber = '#po_number';

    /**
     * Select payment method
     *
     * @param array $payment
     * @return void
     */
    public function selectPaymentMethod(array $payment)
    {
        $this->_rootElement->find(sprintf($this->paymentMethod, $payment['method']))->click();
        if ($payment['method'] == "purchaseorder") {
            $this->_rootElement->find($this->purchaseOrderNumber)->setValue($payment['po_number']);
        }
        if (isset($payment['dataConfig']['payment_form_class'])) {
            $paymentFormClass = $payment['dataConfig']['payment_form_class'];
            /** @var \Magento\Payment\Test\Block\Form\Cc $formBlock */
            $formBlock = $this->blockFactory->create(
                $paymentFormClass,
                ['element' => $this->_rootElement->find('#payment_form_' . $payment['method'])]
            );
            $formBlock->fill($payment['credit_card']);
        }
    }

    /**
     * Press "Continue" button
     *
     * @return void
     */
    public function clickContinue()
    {
        $this->_rootElement->find($this->continue)->click();
        $this->waitForElementNotVisible($this->waitElement);
    }
}
