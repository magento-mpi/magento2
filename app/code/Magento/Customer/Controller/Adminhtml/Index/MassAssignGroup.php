<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Adminhtml\Index;

class MassAssignGroup extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * Customer mass assign group action
     *
     * @return void
     */
    public function execute()
    {
        $customerIds = $this->getRequest()->getParam('customer');
        $customersUpdated = $this->actUponMultipleCustomers(
            function ($customerId) {
                // Verify customer exists
                $customer = $this->_customerRepository->getById($customerId);
                $this->customerDataBuilder->populateWithArray($customer->__toArray());
                $customer = $this->customerDataBuilder->setGroupId($this->getRequest()->getParam('group'))->create();
                $this->_customerRepository->save($customer);
            },
            $customerIds
        );
        if ($customersUpdated) {
            $this->messageManager->addSuccess(__('A total of %1 record(s) were updated.', $customersUpdated));
        }
        $this->_redirect('customer/*/index');
    }
}
