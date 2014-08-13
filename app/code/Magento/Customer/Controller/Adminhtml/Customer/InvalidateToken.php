<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Adminhtml\Customer;

class InvalidateToken extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Integration\Service\V1\TokenService $currentToken */
        $currentToken = $this->_objectManager->get('\Magento\Integration\Service\V1\TokenService');

        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            try {
                $currentToken->revokeCustomerAccessToken($customerId);
                $this->messageManager->addSuccess(__('You have invalidated the customer.'));
                $this->_redirect('adminhtml/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', array('customer_id' => $this->getRequest()->getParam('customer_id')));
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find a customer to invalidate.'));
        $this->_redirect('adminhtml/*/');
    }
}