<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Adminhtml\Index;

class MassUnsubscribe extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * Customer mass unsubscribe action
     *
     * @return void
     */
    public function execute()
    {
        $customerIds = $this->getRequest()->getParam('customer');
        $customersUpdated = $this->actUponMultipleCustomers(
            function ($customerId) {
                // Verify customer exists
                $this->_customerAccountService->getCustomer($customerId);
                $this->_subscriberFactory->create()->unsubscribeCustomerById($customerId);
            },
            $customerIds
        );
        if ($customersUpdated) {
            $this->messageManager->addSuccess(__('A total of %1 record(s) were updated.', $customersUpdated));
        }
        $this->_redirect('customer/*/index');
    }
}
