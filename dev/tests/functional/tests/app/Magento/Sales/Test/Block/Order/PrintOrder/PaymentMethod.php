<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Order\PrintOrder;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class PaymentMethod
 * Payment Method block on order's print page
 */
class PaymentMethod extends Block
{
    /**
     * Payment method selector.
     *
     * @var string
     */
    protected $paymentMethodSelector = './/dt[contains(., "%s")]';

    /**
     * Check if payment method is visible in print order page.
     *
     * @param string $paymentMethod
     * @return bool
     */
    public function isPaymentMethodVisible($paymentMethod)
    {
        return $this->_rootElement->find(
            sprintf($this->paymentMethodSelector, $paymentMethod),
            Locator::SELECTOR_XPATH
        )->isVisible();
    }
}
