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

class CanUseCheckout implements SpecificationInterface
{

    /**
     * Check whether payment method is applicable to quote
     *
     * @param \Magento\Payment\Model\Method\AbstractMethod $paymentMethod
     * @param \Magento\Sales\Model\Quote $quote
     * @return bool
     */
    public function isApplicable(AbstractMethod $paymentMethod, Quote $quote)
    {
        return $paymentMethod->canUseCheckout();
    }
}
