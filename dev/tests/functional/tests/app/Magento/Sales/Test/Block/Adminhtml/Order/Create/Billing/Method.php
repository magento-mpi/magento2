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

namespace Magento\Sales\Test\Block\Adminhtml\Order\Create\Billing;

use Magento\Sales\Test\Fixture\Order;
use Mtf\Block\Block;

/**
 * Class Method
 * Adminhtml sales order create payment method block
 *
 * @package Magento\Sales\Test\Block\Adminhtml\Order\Create\Billing
 */
class Method extends Block
{
    /**
     * Payment method
     *
     * @var string
     */
    protected $paymentMethod = '#p_method_%s';

    /**
     * Select payment method
     *
     * @param Order $fixture
     */
    public function selectPaymentMethod(Order $fixture)
    {
        $payment = $fixture->getPaymentMethod();
        $paymentCode = $payment->getPaymentCode();
        $paymentInput = $this->_rootElement->find(sprintf($this->paymentMethod, $paymentCode));
        if ($paymentInput->isVisible()) {
            $paymentInput->click();
        }
    }
}
