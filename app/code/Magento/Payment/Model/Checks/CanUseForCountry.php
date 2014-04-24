<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Payment\Model\Checks;

use Magento\Sales\Model\Quote;

class CanUseForCountry implements SpecificationInterface
{
    /**
     * Check whether payment method is applicable to quote
     * @param PaymentMethodInterface $paymentMethod
     * @param \Magento\Sales\Model\Quote $quote
     * @return bool
     */
    public function isApplicable(PaymentMethodInterface $paymentMethod, Quote $quote)
    {
        return $paymentMethod->canUseForCountry($quote->getBillingAddress()->getCountry());
    }
}
