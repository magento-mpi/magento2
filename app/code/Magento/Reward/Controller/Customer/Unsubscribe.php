<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Controller\Customer;

class Unsubscribe extends \Magento\Reward\Controller\Customer
{
    /**
     * Unsubscribe customer from update/warning balance notifications
     *
     * @return void
     */
    public function execute()
    {
        $notification = $this->getRequest()->getParam('notification');
        if (!in_array($notification, ['update', 'warning'])) {
            $this->_forward('noroute');
        }

        try {
            /* @var $customer \Magento\Customer\Model\Session */
            $customer = $this->_getCustomer();
            if ($customer->getId()) {
                if ($notification == 'update') {
                    $customer->setRewardUpdateNotification(false);
                    $customer->getResource()->saveAttribute($customer, 'reward_update_notification');
                } elseif ($notification == 'warning') {
                    $customer->setRewardWarningNotification(false);
                    $customer->getResource()->saveAttribute($customer, 'reward_warning_notification');
                }
                $this->messageManager->addSuccess(__('You have been unsubscribed.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Failed to unsubscribe'));
        }

        $this->_redirect('*/*/info');
    }
}
