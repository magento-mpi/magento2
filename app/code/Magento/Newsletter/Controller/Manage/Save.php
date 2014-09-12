<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Controller\Manage;

class Save extends \Magento\Newsletter\Controller\Manage
{
    /**
     * Save newsletter subscription preference action
     *
     * @return void|null
     */
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->_redirect('customer/account/');
        }

        $customerId = $this->_customerSession->getCustomerId();
        if (is_null($customerId)) {
            $this->messageManager->addError(__('Something went wrong while saving your subscription.'));
        } else {
            try {
                $customer = $this->_customerAccountService->getCustomer($customerId);
                $storeId = $this->_storeManager->getStore()->getId();
                $customerDetails = $this->_customerDetailsBuilder->setAddresses(null)
                    ->setCustomer($this->_customerBuilder->populate($customer)->setStoreId($storeId)->create())
                    ->create();
                $this->_customerAccountService->updateCustomer($customerId, $customerDetails);

                if ((boolean)$this->getRequest()->getParam('is_subscribed', false)) {
                    $this->_subscriberFactory->create()->subscribeCustomerById($customerId);
                    $this->messageManager->addSuccess(__('We saved the subscription.'));
                } else {
                    $this->_subscriberFactory->create()->unsubscribeCustomerById($customerId);
                    $this->messageManager->addSuccess(__('We removed the subscription.'));
                }
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong while saving your subscription.'));
            }
        }
        $this->_redirect('customer/account/');
    }
}
