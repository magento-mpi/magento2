<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Payment\Model\Checks;

use Magento\Sales\Model\Quote;

class CanUseForCurrency implements SpecificationInterface
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
        return $paymentMethod->canUseForCurrency($quote->getStore()->getBaseCurrencyCode());
    }
}
