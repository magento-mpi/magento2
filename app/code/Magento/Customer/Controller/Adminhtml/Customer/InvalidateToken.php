<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Controller\Adminhtml\Customer;

/**
 *  Class to invalidate tokens for customers
 */
class InvalidateToken extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_tokenService;

    /**
     * @param \Magento\Integration\Service\V1\TokenService $tokenService
     */
    public function __construct(\Magento\Integration\Service\V1\TokenService $tokenService)
    {
        $this->_tokenService = $tokenService;
    }

    /**
     * Reset customer's tokens handler
     *
     * @return void
     */
    public function execute()
    {
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            try {
                $this->_tokenService->revokeCustomerAccessToken($customerId);
                $this->messageManager->addSuccess(__('You have invalidated customer token.'));
                $this->_redirect('customer/*/edit', array('customer_id' => $customerId, '_current' => true));
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', array('customer_id' => $this->getRequest()->getParam('customer_id')));
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find a customer to invalidate token.'));
        $this->_redirect('adminhtml/*/');
    }
}