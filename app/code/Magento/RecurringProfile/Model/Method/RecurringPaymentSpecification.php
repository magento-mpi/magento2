<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\RecurringProfile\Model\Method;

use Magento\Payment\Model\Method\Specification\AbstractSpecification;

/**
 * Enable method specification
 */
class RecurringPaymentSpecification extends AbstractSpecification
{
    /**
     * Allow multiple address flag
     */
    const CONFIG_KEY = 'support_recurring_payment';

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($paymentMethod)
    {
        return isset($this->methodsInfo[$paymentMethod][self::CONFIG_KEY])
            && $this->methodsInfo[$paymentMethod][self::CONFIG_KEY];
    }
}
