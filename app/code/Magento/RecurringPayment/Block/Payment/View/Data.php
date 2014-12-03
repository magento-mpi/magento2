<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Block\Payment\View;

/**
 * Recurring payment view data
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Data extends \Magento\RecurringPayment\Block\Payment\View
{
    /**
     * Prepare payment data
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->addData(
            array(
                'reference_id' => $this->_recurringPayment->getReferenceId(),
                'can_cancel' => $this->_recurringPayment->canCancel(),
                'cancel_url' => $this->getUrl(
                    '*/*/updateState',
                    array('payment' => $this->_recurringPayment->getId(), 'action' => 'cancel')
                ),
                'can_suspend' => $this->_recurringPayment->canSuspend(),
                'suspend_url' => $this->getUrl(
                    '*/*/updateState',
                    array('payment' => $this->_recurringPayment->getId(), 'action' => 'suspend')
                ),
                'can_activate' => $this->_recurringPayment->canActivate(),
                'activate_url' => $this->getUrl(
                    '*/*/updateState',
                    array('payment' => $this->_recurringPayment->getId(), 'action' => 'activate')
                ),
                'can_update' => $this->_recurringPayment->canFetchUpdate(),
                'update_url' => $this->getUrl(
                    '*/*/updatePayment',
                    array('payment' => $this->_recurringPayment->getId())
                ),
                'back_url' => $this->getUrl('*/*/'),
                'confirmation_message' => __('Are you sure you want to do this?')
            )
        );
    }
}
