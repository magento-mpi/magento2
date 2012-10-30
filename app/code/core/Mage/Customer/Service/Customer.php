<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer service is responsible for customer business workflows encapsulation
 *
 * @category    Mage
 * @package     Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Service_Customer extends Mage_Core_Service_ServiceAbstract
{
    /**
     * Constructor
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        if (!isset($args['helper'])) {
            $args['helper'] = Mage::helper('Mage_Customer_Helper_Data');
        }
        parent::__construct($args);
    }

    /**
     * Create customer entity
     *
     * @param array $customerData
     * @return Mage_Customer_Model_Customer
     */
    public function create($customerData)
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer');
        $this->_setDataUsingMethods($customer, $customerData);
        $this->_preparePasswordForSave($customer, $customerData);
        $this->_save($customer, $customerData, 'create');

        return $customer;
    }

    /**
     * Update customer entity
     *
     * @param string|int $customerId
     * @param array $customerData
     * @return Mage_Customer_Model_Customer
     */
    public function update($customerId, $customerData)
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = $this->_loadCustomerById($customerId);

        if (!empty($customerData)) {
            $this->_setDataUsingMethods($customer, $customerData);
            $this->_save($customer, $customerData, 'update');
            $this->_changePassword($customer, $customerData);
        }

        return $customer;
    }

    /**
     * Save customer entity. Perform supplementary business workflow actions
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param array $customerData
     * @param string $validatorGroup
     * @return Mage_Customer_Service_Customer
     */
    protected function _save($customer, $customerData, $validatorGroup)
    {
        $customer->save();
        $this->_sendWelcomeEmail($customer, $customerData);

        return $this;
    }

    /**
     * Set customer password
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param array $customerAccountData
     */
    protected function _preparePasswordForSave($customer, $customerAccountData)
    {
        $password = $this->_getCustomerPassword($customer, $customerAccountData);
        if (!is_null($password)) {
            // 'force_confirmed' should be set in admin area only
            if (Mage::app()->getStore()->getId() == Mage_Core_Model_App::ADMIN_STORE_ID) {
                $customer->setForceConfirmed(true);
            }
            $customer->setPassword($password);
        }
    }

    /**
     * Get customer password
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param array $customerAccountData
     * @return string|null
     */
    protected function _getCustomerPassword($customer, $customerAccountData)
    {
        $password = null;

        if ($this->_isAutogeneratePassword($customerAccountData)) {
            $password = $customer->generatePassword();
        } elseif (isset($customerAccountData['password'])) {
            $password = $customerAccountData['password'];
        }

        return $password;
    }

    /**
     * Change customer password
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param array $customerAccountData
     * @return Mage_Customer_Service_Customer
     */
    protected function _changePassword($customer, $customerAccountData)
    {
        $passwordSet = isset($customerAccountData['password']) && !empty($customerAccountData['password']);
        $autogeneratePassword = $this->_isAutogeneratePassword($customerAccountData);

        if ($passwordSet || $autogeneratePassword) {
            $newPassword = $this->_getCustomerPassword($customer, $customerAccountData);
            $customer->changePassword($newPassword)
                ->sendPasswordReminderEmail();
        }

        return $this;
    }

    /**
     * Check if password should be generated automatically
     *
     * @param array $customerAccountData
     * @return bool
     */
    protected function _isAutogeneratePassword($customerAccountData)
    {
        return isset($customerAccountData['autogenerate_password']) && $customerAccountData['autogenerate_password'];
    }

    /**
     * Retrieve send email flag
     *
     * @param array $customerAccountData
     * @return bool
     */
    protected function _isSendEmail($customerAccountData)
    {
        return isset($customerAccountData['sendemail']) && $customerAccountData['sendemail'];
    }

    /**
     * Send welcome email to customer
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param array $accountData
     * @return Mage_Customer_Service_Customer
     */
    protected function _sendWelcomeEmail($customer, $accountData)
    {
        if ($customer->getWebsiteId()
            && ($this->_isSendEmail($accountData) || $this->_isAutogeneratePassword($accountData))) {

            $isNewCustomer = $customer->isObjectNew();
            $storeId = $customer->getSendemailStoreId();

            if ($isNewCustomer) {
                $customer->sendNewAccountEmail('registered', '', $storeId);
            } elseif ((!$customer->getConfirmation())) {
                // Confirm not confirmed customer
                $customer->sendNewAccountEmail('confirmed', '', $storeId);
            }
        }
        return $this;
    }

    /**
     * Load customer by its ID
     *
     * @param int|string $customerId
     * @return Mage_Customer_Model_Customer
     * @throws Mage_Core_Exception
     */
    protected function _loadCustomerById($customerId)
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer');
        $customer->load($customerId);
        if (!$customer->getId()) {
            throw new Mage_Core_Exception($this->_translateHelper->__("The customer with the specified ID not found."));
        }

        return $customer;
    }
}
