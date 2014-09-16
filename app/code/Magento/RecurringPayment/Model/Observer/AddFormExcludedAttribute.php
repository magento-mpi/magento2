<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model\Observer;

class AddFormExcludedAttribute
{
    /**
     * Add recurring payment field to excluded list
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute($observer)
    {
        $block = $observer->getEvent()->getObject();

        $block->setFormExcludedFieldList(array_merge($block->getFormExcludedFieldList(), array('recurring_payment')));
    }
}
