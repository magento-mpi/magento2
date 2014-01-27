<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Multishipping\Model\Payment\Method;

/**
 * Interface SpecificationInterface
 */
interface SpecificationInterface
{
    /**
     * Check is payment method available for multishipping
     *
     * @param string $paymentMethod
     * @return bool
     */
    public function isSatisfiedBy($paymentMethod);
}
