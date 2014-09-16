<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model\Observer;

class UnserializeProductRecurringPayment
{
    /**
     * Unserialize product recurring payment
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute($observer)
    {
        $collection = $observer->getEvent()->getCollection();

        foreach ($collection as $product) {
            $payment = $product->getRecurringPayment();
            if ($product->getIsRecurring() && $payment) {
                $product->setRecurringPayment(unserialize($payment));
            }
        }
    }
}
