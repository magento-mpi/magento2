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
     * @var callable
     */
    protected $_beforeSaveCallback = null;

    /**
     * @var callable
     */
    protected $_afterSaveCallback = null;

    /**
     * @var Mage_Customer_Helper_Data
     */
    protected $_translateHelper = null;

    /**
     * @var Mage_Customer_Model_CustomerFactory
     */
    protected $_customerFactory = null;

    /**
     * @var Mage_Customer_Model_AddressFactory
     */
    protected $_addressFactory = null;

    /**
     * @var int
     */
    protected $_storeId = null;

    /**
     * Constructor
     *
     * @param Mage_Customer_Helper_Data $helper
     * @param Mage_Customer_Model_CustomerFactory $customerFactory
     * @param Mage_Customer_Model_AddressFactory $addressFactory
     * @param int $storeId
     */
    public function __construct(
        Mage_Customer_Helper_Data $helper,
        Mage_Customer_Model_CustomerFactory $customerFactory,
        Mage_Customer_Model_AddressFactory $addressFactory,
        $storeId = Mage_Core_Model_App::ADMIN_STORE_ID
    ) {
        $this->_translateHelper = $helper;
        $this->_customerFactory = $customerFactory;
        $this->_addressFactory = $addressFactory;
        $this->_storeId = $storeId;
    }

    /**
     * Set store id.
     *
     * @param int $storeId
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
    }

    /**
     * Set before save callback.
     *
     * @param callable $callback
     * @return Mage_Customer_Service_Customer
     */
    public function setBeforeSaveCallback($callback)
    {
        $this->_beforeSaveCallback = $callback;
        return $this;
    }

    /**
     * Set after save callback.
     *
     * @param callable $callback
     * @return Mage_Customer_Service_Customer
     */
    public function setAfterSaveCallback($callback)
    {
        $this->_afterSaveCallback = $callback;
        return $this;
    }

    /**
     * Create customer with optional ability of adding addresses.
     *
     * @param array $customerData
     * @param array|null $addressesData array of addresses
     * @return Mage_Customer_Model_Customer
     */
    public function create(array $customerData, array $addressesData = null)
    {
        $customer = $this->_customerFactory->create();
        $this->_preparePasswordForSave($customer, $customerData);
        $this->_save($customer, $customerData, $addressesData);

        return $customer;
    }

    /**
     * Update customer entity.
     *
     * Update customer with optional ability to update customer addresses.
     * Addresses that are not in $addressesData array but present in customer addresses collection will be removed.
     * New address is created in case when no entity_id is present, otherwise corresponding address will be updated
     * with data provided.
     *
     * @param string|int $customerId
     * @param array $customerData
     * @param array|null $addressesData array of addresses
     * @return Mage_Customer_Model_Customer
     */
    public function update($customerId, array $customerData, array $addressesData = null)
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = $this->_loadCustomerById($customerId);

        $this->_save($customer, $customerData, $addressesData);
        if ($customerData) {
            $this->_changePassword($customer, $customerData);
        }

        return $customer;
    }

    /**
     * Save customer entity. Perform supplementary business workflow actions
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param array $customerData
     * @param array|null $addressesData
     * @return Mage_Customer_Service_Customer
     */
    protected function _save($customer, array $customerData, array $addressesData = null)
    {
        if ($customerData) {
            $this->_setDataUsingMethods($customer, $customerData);
        }
        $this->_beforeSave($customer, $customerData, $addressesData);
        $customer->save();
        $this->_afterSave($customer, $customerData, $addressesData);

        return $this;
    }

    /**
     * Trigger before save logic
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param array $customerData
     * @param array $addressesData
     */
    protected function _beforeSave($customer, array $customerData, array $addressesData = null)
    {
        if (!is_null($addressesData)) {
            $this->_prepareCustomerAddressesForSave($customer, $addressesData);
        }
        if (is_callable($this->_beforeSaveCallback)) {
            call_user_func_array($this->_beforeSaveCallback, array($customer, $customerData, $addressesData));
        }
    }

    /**
     * Trigger before save logic
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param array $customerData
     * @param array $addressesData
     */
    protected function _afterSave($customer, array $customerData, array $addressesData = null)
    {
        if (is_callable($this->_afterSaveCallback)) {
            call_user_func_array($this->_afterSaveCallback, array($customer, $customerData, $addressesData));
        }
        $this->_sendWelcomeEmail($customer, $customerData);
    }

    /**
     * Set customer password
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param array $customerData
     */
    protected function _preparePasswordForSave($customer, array $customerData)
    {
        $password = $this->_getCustomerPassword($customer, $customerData);
        if (!is_null($password)) {
            // 'force_confirmed' should be set in admin area only
            if ($this->_storeId == Mage_Core_Model_App::ADMIN_STORE_ID) {
                $customer->setForceConfirmed(true);
            }
            $customer->setPassword($password);
        }
    }

    /**
     * Get customer password
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param array $customerData
     * @return string|null
     */
    protected function _getCustomerPassword($customer, array $customerData)
    {
        $password = null;

        if ($this->_isAutogeneratePassword($customerData)) {
            $password = $customer->generatePassword();
        } elseif (isset($customerData['password'])) {
            $password = $customerData['password'];
        }

        return $password;
    }

    /**
     * Change customer password
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param array $customerData
     * @return Mage_Customer_Service_Customer
     */
    protected function _changePassword($customer, array $customerData)
    {
        if (!empty($customerData['password']) || $this->_isAutogeneratePassword($customerData)) {
            $newPassword = $this->_getCustomerPassword($customer, $customerData);
            $customer->changePassword($newPassword)
                ->sendPasswordReminderEmail();
        }

        return $this;
    }

    /**
     * Check if password should be generated automatically
     *
     * @param array $customerData
     * @return bool
     */
    protected function _isAutogeneratePassword(array $customerData)
    {
        return isset($customerData['autogenerate_password']) && $customerData['autogenerate_password'];
    }

    /**
     * Retrieve send email flag
     *
     * @param array $customerData
     * @return bool
     */
    protected function _isSendEmail(array $customerData)
    {
        return isset($customerData['sendemail']) && $customerData['sendemail'];
    }

    /**
     * Send welcome email to customer
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param array $customerData
     * @return Mage_Customer_Service_Customer
     */
    protected function _sendWelcomeEmail($customer, array $customerData)
    {
        if ($customer->getWebsiteId()
            && ($this->_isSendEmail($customerData) || $this->_isAutogeneratePassword($customerData))
        ) {
            $isNewCustomer = !(bool)$customer->getOrigData($customer->getIdFieldName());
            $storeId = $customer->getSendemailStoreId();

            if ($isNewCustomer) {
                $customer->sendNewAccountEmail('registered', '', $storeId);
            } elseif (!$customer->getConfirmation()) {
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
        $customer = $this->_customerFactory->create();
        $customer->load($customerId);
        if (!$customer->getId()) {
            throw new Mage_Core_Exception($this->_translateHelper->__("The customer with the specified ID not found."));
        }

        return $customer;
    }

    /**
     * Save customer addresses.
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param array $addressesData
     * @throws Mage_Core_Exception
     */
    protected function _prepareCustomerAddressesForSave($customer, array $addressesData)
    {
        $hasChanges = $customer->hasDataChanges();
        $actualAddressesIds = array();
        foreach ($addressesData as $addressData) {
            $addressId = null;
            if (array_key_exists('entity_id', $addressData)) {
                $addressId = $addressData['entity_id'];
                unset($addressData['entity_id']);
            }

            if (null !== $addressId) {
                $address = $customer->getAddressItemById($addressId);
                if (!$address || !$address->getId()) {
                    throw new Mage_Core_Exception(
                        $this->_translateHelper->__('The address with the specified ID not found.'));
                }
            } else {
                $address = $this->_addressFactory->create();
                $address->setCustomerId($customer->getId());
                // Add customer address into addresses collection
                $customer->addAddress($address);
            }
            $address->addData($addressData);
            $hasChanges = $hasChanges || $address->hasDataChanges();

            // Set post_index for detect default billing and shipping addresses
            $address->setPostIndex($addressId);

            $actualAddressesIds[] = $address->getId();
        }

        /** @var Mage_Customer_Model_Address $address */
        foreach ($customer->getAddressesCollection() as $address) {
            if (!in_array($address->getId(), $actualAddressesIds)) {
                $address->setData('_deleted', true);
                $hasChanges = true;
            }
        }
        $customer->setDataChanges($hasChanges);
    }
}
