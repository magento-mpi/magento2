<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringProfile\Model\Plugin;

use \Magento\Sales\Model\Quote;
use \Magento\Payment\Model\Method\AbstractMethod;
use \Magento\RecurringProfile\Model\Method\RecurringPaymentSpecification;

/**
 * ZeroTotal checker plugin
 * Allow ZeroTotal for requring payment
 */
class ZeroTotal
{
    /** @var  \Magento\RecurringProfile\Model\Quote\Filte */
    protected $filter;

    /** @var  RecurringPaymentSpecification */
    protected $specification;

    /**
     * @param \Magento\RecurringProfile\Model\Quote\Filter $filter
     * @param RecurringPaymentSpecification $specification
     */
    public function __construct(
        \Magento\RecurringProfile\Model\Quote\Filter $filter,
        RecurringPaymentSpecification $specification
    ) {
        $this->filter = $filter;
        $this->specification = $specification;
    }

    /**
     * @param \Magento\Payment\Model\Checks\ZeroTotal $subject
     * @param callable $proceed
     * @param AbstractMethod $paymentMethod
     * @param Quote $quote
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundIsApplicable(
        \Magento\Payment\Model\Checks\ZeroTotal $subject,
        \Closure $proceed,
        AbstractMethod $paymentMethod,
        Quote $quote
    ) {
        return $proceed($paymentMethod, $quote) || ($this->specification->isSatisfiedBy($paymentMethod->getCode())
            && $this->filter->hasRecurringItems($quote));
    }
} 