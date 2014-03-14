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

use \Magento\Sales\Model\Quote;
use \Magento\Payment\Model\Method\AbstractMethod;

class CanUseForCurrency implements SpecificationInterface
{
    /**
     * Check whether payment method is applicable to quote
     * Purposed to allow use in controllers some logic that was implemented in blocks only before
     *
     * @param \Magento\Payment\Model\Method\AbstractMethod $paymentMethod
     * @param \Magento\Sales\Model\Quote $quote
     * @return bool
     */
    public function isApplicable(AbstractMethod $paymentMethod, Quote $quote)
    {
        return $paymentMethod->canUseForCurrency($quote->getStore()->getBaseCurrencyCode());
    }
}
