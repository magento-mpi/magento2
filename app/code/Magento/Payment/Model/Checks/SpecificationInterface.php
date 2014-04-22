<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Payment\Model\Checks;

use Magento\Sales\Model\Quote;
use Magento\Payment\Model\Method\AbstractMethod;

/**
 * Payment method abstract model
 */
interface SpecificationInterface
{
    /**
     * Check whether payment method is applicable to quote
     *
     * @param \Magento\Payment\Model\Method\AbstractMethod $paymentMethod
     * @param \Magento\Sales\Model\Quote $quote
     * @return bool
     */
    public function isApplicable(AbstractMethod $paymentMethod, Quote $quote);
}
