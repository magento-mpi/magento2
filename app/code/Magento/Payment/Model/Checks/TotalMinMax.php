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
use Magento\Payment\Model\MethodInterface;

class TotalMinMax implements SpecificationInterface
{
    /**
     * Check whether payment method is applicable to quote
     *
     * @param \Magento\Payment\Model\MethodInterface $paymentMethod
     * @param \Magento\Sales\Model\Quote $quote
     * @return bool
     */
    public function isApplicable(MethodInterface $paymentMethod, Quote $quote)
    {
        $total = $quote->getBaseGrandTotal();
        $minTotal = $paymentMethod->getConfigData('min_order_total');
        $maxTotal = $paymentMethod->getConfigData('max_order_total');
        if (!empty($minTotal) && $total < $minTotal || !empty($maxTotal) && $total > $maxTotal) {
            return false;
        }
        return true;
    }
}
