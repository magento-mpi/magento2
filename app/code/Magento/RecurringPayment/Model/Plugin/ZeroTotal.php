<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model\Plugin;

use Magento\Sales\Model\Quote;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\RecurringPayment\Model\Method\RecurringPaymentSpecification;

/**
 * ZeroTotal checker plugin
 * Allow ZeroTotal for recurring payment
 */
class ZeroTotal
{
    /** @var  \Magento\RecurringPayment\Model\Quote\Filter */
    protected $filter;

    /** @var  RecurringPaymentSpecification */
    protected $specification;

    /**
     * @param \Magento\RecurringPayment\Model\Quote\Filter $filter
     * @param RecurringPaymentSpecification $specification
     */
    public function __construct(
        \Magento\RecurringPayment\Model\Quote\Filter $filter,
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
        return $proceed(
            $paymentMethod,
            $quote
        ) || $this->specification->isSatisfiedBy(
            $paymentMethod->getCode()
        ) && $this->filter->hasRecurringItems(
            $quote
        );
    }
}
