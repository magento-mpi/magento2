<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model\Observer;

use \Magento\Sales\Model\Quote;

class PaymentAvailabilityObserver
{
    /** @var  \Magento\RecurringPayment\Model\Quote\Filter */
    protected $quoteFilter;
    /** @var  \Magento\RecurringPayment\Model\Method\RecurringPaymentSpecification */
    protected $specification;

    /**
     * @param \Magento\RecurringPayment\Model\Quote\Filter $quoteFilter
     * @param \Magento\RecurringPayment\Model\Method\RecurringPaymentSpecification $specification
     */
    public function __construct(
        \Magento\RecurringPayment\Model\Quote\Filter $quoteFilter,
        \Magento\RecurringPayment\Model\Method\RecurringPaymentSpecification $specification
    ) {
        $this->quoteFilter = $quoteFilter;
        $this->specification = $specification;
    }

    /**
     * @param \Magento\Event\Observer $observer
     * @return void
     */
    public function observe(\Magento\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        /** @var \Magento\Payment\Model\Method\AbstractMethod $paymentMethod */
        $paymentMethod = $observer->getEvent()->getMethodInstance();
        $result = $observer->getEvent()->getResult();

        if ($quote
            && $this->quoteFilter->hasRecurringItems($quote)
            && !$this->specification->isSatisfiedBy($paymentMethod->getCode())
        ) {
            $result->isAvailable = false;
        }
    }
}
