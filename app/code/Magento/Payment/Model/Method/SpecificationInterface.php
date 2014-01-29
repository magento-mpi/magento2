<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Payment\Model\Method;

/**
 * Interface SpecificationInterface
 */
interface SpecificationInterface
{
    /**
     * Check specification is satisfied by payment method
     *
     * @param string $paymentMethod
     * @return bool
     */
    public function isSatisfiedBy($paymentMethod);
}
