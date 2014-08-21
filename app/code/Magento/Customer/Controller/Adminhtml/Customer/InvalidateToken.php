<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Controller\Adminhtml\Customer;

use Magento\Customer\Service\V1\Data\CustomerBuilder;
use Magento\Customer\Service\V1\CustomerAddressServiceInterface;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Customer\Service\V1\Data\AddressBuilder;
use Magento\Customer\Service\V1\Data\CustomerDetailsBuilder;

/**
 *  Class to invalidate tokens for customers
 */
class InvalidateToken extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * Reset customer's tokens handler
     *
     * @return void
     */
    public function execute()
    {
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            try {
                /** @var \Magento\Integration\Service\V1\TokenService $tokenService */
                $tokenService = $this->_objectManager->get('Magento\Integration\Service\V1\TokenService');
                $tokenService->revokeCustomerAccessToken($customerId);
                $this->messageManager->addSuccess(__('You have revoked the customer\'s tokens.'));
                $this->_redirect('customer/index/edit', array('id' => $customerId, '_current' => true));
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('customer/index/edit', array('id' => $customerId, '_current' => true));
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find a customer to revoke.'));
        $this->_redirect('customer/index/index');
    }
}
