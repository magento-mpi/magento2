<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Payment\Model\Checks;

use Magento\Sales\Model\Quote;
use Magento\Payment\Model\MethodInterface;

/**
 * Payment method abstract model
 */
interface SpecificationInterface
{
    /**
     * Check whether payment method is applicable to quote
     *
     * @param \Magento\Payment\Model\MethodInterface $paymentMethod
     * @param \Magento\Sales\Model\Quote $quote
     * @return bool
     */
    public function isApplicable(MethodInterface $paymentMethod, Quote $quote);
}
