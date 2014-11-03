<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Controller\Customer\Account;

use \Magento\Framework\Exception\NoSuchEntityException;
use Magento\Invitation\Controller\Customer\AccountInterface;

class Confirm extends \Magento\Customer\Controller\Account\Confirm implements AccountInterface
{
    /**
     * Load customer by id (try/catch in case if it throws exceptions)
     *
     * @param int $customerId
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Exception
     */
    protected function _loadCustomerById($customerId)
    {
        try {
            /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
            return $this->customerAccountService->getCustomer($customerId);
        } catch (NoSuchEntityException $e) {
            throw new \Exception(__('Wrong customer account specified.'));
        }
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param mixed $key
     * @return bool|null
     * @throws \Exception
     */
    protected function _checkCustomerActive($customer, $key)
    {
        if ($customer->getConfirmation()) {
            if ($customer->getConfirmation() !== $key) {
                throw new \Exception(__('Wrong confirmation key.'));
            }
            $this->customerAccountService->activateCustomer($customer->getId(), $key);

            // log in and send greeting email, then die happy
            $this->_getSession()->setCustomerAsLoggedIn($customer);
            $this->_redirect('customer/account/');
            return true;
        }
    }

    /**
     * Confirm customer account by id and confirmation key
     *
     * @return void
     */
    public function execute()
    {
        if ($this->_getSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        try {
            $customerId = $this->getRequest()->getParam('id', false);
            $key = $this->getRequest()->getParam('key', false);
            if (empty($customerId) || empty($key)) {
                throw new \Exception(__('Bad request.'));
            }

            $customer = $this->_loadCustomerById($customerId);
            if (true === $this->_checkCustomerActive($customer, $key)) {
                return;
            }
            // die happy
            $this->_redirect('customer/account/');
            return;
        } catch (\Exception $e) {
            // die unhappy
            $this->messageManager->addError($e->getMessage());
            $this->_redirect(
                'magento_invitation/customer_account/create',
                array('_current' => true, '_secure' => true)
            );
            return;
        }
    }
}
