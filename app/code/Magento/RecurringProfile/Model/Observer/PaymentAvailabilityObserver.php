<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringProfile\Model\Observer;

use \Magento\Sales\Model\Quote;

class PaymentAvailabilityObserver
{
    /** @var  \Magento\RecurringProfile\Model\Quote\Filter */
    protected $quoteFilter;

    /**
     * @param \Magento\RecurringProfile\Model\Quote\Filter $quoteFilter
     */
    public function __construct(\Magento\RecurringProfile\Model\Quote\Filter $quoteFilter)
    {
        $this->quoteFilter = $quoteFilter;
    }

    /**
     * @param \Magento\Payment\Model\Method\AbstractMethod $paymentMethod
     * @return bool
     */
    private function canManageRecurringProfiles(\Magento\Payment\Model\Method\AbstractMethod $paymentMethod)
    {
        return $paymentMethod instanceof \Magento\Payment\Model\Recurring\Profile\MethodInterface;
    }

    /**
     * @param \Magento\Event\Observer $observer
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
            && !$this->canManageRecurringProfiles($paymentMethod)
        ) {
            $result->isAvailable = false;
        }
    }
}
 