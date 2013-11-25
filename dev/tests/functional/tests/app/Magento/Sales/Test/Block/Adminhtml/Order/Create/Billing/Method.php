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

use Magento\Backend\Test\Block\Template;
use Magento\Sales\Test\Fixture\Order;
use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;

/**
 * Class Method
 * Adminhtml sales order create payment method block
 *
 * @package Magento\Sales\Test\Block\Adminhtml\Order\Create\Billing
 */
class Method extends Block
{
    /**
     * Select payment method
     *
     * @param Order $fixture
     */
    public function selectPaymentMethod(Order $fixture)
    {
        $payment = $fixture->getPaymentMethod();
        $paymentCode = $payment->getPaymentCode();
        $paymentInput = $this->_rootElement->find('#p_method_' . $paymentCode, Locator::SELECTOR_CSS);
        if ($paymentInput->isVisible()) {
            $paymentInput->click();
        }
    }
}
