<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model\Observer;

class SetFormRecurringElementRenderer
{
    /**
     * Set recurring payment renderer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute($observer)
    {
        $form = $observer->getEvent()->getForm();

        $recurringPaymentElement = $form->getElement('recurring_payment');
        $recurringPaymentBlock = $observer->getEvent()->getLayout()->createBlock(
            'Magento\RecurringPayment\Block\Adminhtml\Product\Edit\Tab\Price\Recurring'
        );

        if ($recurringPaymentElement) {
            $recurringPaymentElement->setRenderer($recurringPaymentBlock);
        }
    }
}
