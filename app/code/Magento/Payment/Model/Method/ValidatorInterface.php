<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Model\Method;

interface ValidatorInterface
{
    /**
     * Validates payment method
     *
     * @param AbstractMethod $paymentMethod
     * @throws \Magento\Framework\Model\Exception
     * @return void
     */
    public function validate(AbstractMethod $paymentMethod);
}
