<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Payment\Model\Checks;

use Magento\Sales\Model\Quote;

class ZeroTotal implements SpecificationInterface
{
    /**
     * Check whether payment method is applicable to quote
     * Purposed to allow use in controllers some logic that was implemented in blocks only before
     *
     * @param PaymentMethodChecksInterface $paymentMethod
     * @param \Magento\Sales\Model\Quote $quote
     * @return bool
     */
    public function isApplicable(PaymentMethodChecksInterface $paymentMethod, Quote $quote)
    {
        $total = $quote->getBaseSubtotal() + $quote->getShippingAddress()->getBaseShippingAmount();
        return !($total < 0.0001 && $paymentMethod->getCode() != 'free');
    }
}
