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
     * Check is specification satisfied by payment
     *
     * @param string $paymentMethod
     * @return bool
     */
    public function isSatisfiedBy($paymentMethod);
}
