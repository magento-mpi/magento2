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
 * TODO: Think about welcome emails during customer create and update from backend and frontend.
 * Currently these emails are sent in all cases, however in the native implementation they were not sent during customer update from frontend
 */

/**
 * Customer service is responsible for customer business workflows encapsulation
 *
 * @category    Mage
 * @package     Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Service_Customer
{
    /**
     * @var Mage_Core_Helper_Abstract
     */
    protected $_translateHelper;

    /**
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * List of deprecated attributes
     *
     * @var array
     */
    protected $_deprecatedAttributes = array('store_id', 'website_id');

    /**
     * Constructor
     */
    function __construct()
    {
        $this->_translateHelper = Mage::helper('Mage_Customer_Helper_Data');
        $this->_eventManager = Mage::getSingleton('Mage_Core_Model_Event_Manager');
    }

    /**
     * Customer entity getter.
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return $this->_customer;
    }

    /**
     * Create customer entity. Customer Addresses are also processed
     *
     * @param array $customerData
     * @return Mage_Customer_Model_Customer
     */
    public function create($customerData)
    {
        $accountData = $this->_extractAccountData($customerData);

        // TODO: Move website_id and created_in initialization with default values to attribute
        $accountData['website_id'] = isset($accountData['website_id']) ? $accountData['website_id']
            : Mage::app()->getStore()->getWebsiteId();
        $accountData['created_in'] = (isset($accountData['created_in']) && $accountData['created_in'])
            ? $accountData['created_in'] : Mage::app()->getDefaultStoreView()->getName();
        $accountData['store_id'] = isset($accountData['store_id']) ? $accountData['store_id']
            : Mage::app()->getStore()->getWebsiteId();

        /** @var Mage_Customer_Model_Customer $customer */
        $this->_customer = Mage::getModel('Mage_Customer_Model_Customer');
        $this->_customer->setData($accountData);
         // Initialize customer group id if it was not set
        $this->_customer->getGroupId();
        $this->_preparePasswordForSave($accountData);
        $this->_save($customerData);
        return $this->_customer;
    }

    /**
     * Update customer entity. Customer Address are also processed.
     *
     * @param string|int $customerId
     * @param array $customerData
     * @param bool $deleteMissingAddresses
     * @return Mage_Customer_Model_Customer
     */
    public function update($customerId, $customerData, $deleteMissingAddresses = false)
    {
        $accountData = $this->_extractAccountData($customerData);

        // TODO: Move this logic to attributes
        unset($accountData['created_in']);
        unset($accountData['website_id']);

        $this->_customer = $this->_loadCustomerById($customerId);
        $this->_customer->addData($accountData);
        if ($deleteMissingAddresses) {
            $this->_markMissingAddressesForDelete($customerData);
        }
        $this->_save($customerData);
        $this->_changePassword($accountData);
        return $this->_customer;
    }

    /**
     * Delete customer entity. Customer Address are also processed.
     *
     * @param string|int $customerId
     */
    public function delete($customerId)
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer');
        $customer->load($customerId);
        $customer->delete();
    }

    /**
     * Save customer entity. Perform supplementary business workflow actions
     *
     * @param array $customerData
     * @return Mage_Customer_Service_Customer
     */
    protected function _save($customerData)
    {
        $this->_eventManager->dispatch(
            'customer_service_customer_save_before',
            array('customer' => $this->_customer, 'customer_data' => $customerData)
        );

        $this->_getAttributesToValidate($this->_customer,
            $this->_deprecatedAttributes);

        $accountData = $this->_extractAccountData($customerData);
        $addresses = $this->_extractAddressesData($customerData);
        if (!empty($addresses)) {
            // TODO: Think about better implementation of default billing and shipping addresses setting
            // Set default billing and shipping flags to address
            $defaultBilling = isset($accountData['default_billing']) ? $accountData['default_billing'] : null;
            $defaultShipping = isset($accountData['default_shipping']) ? $accountData['default_shipping'] : null;
            $this->_processAddresses($addresses, $defaultBilling, $defaultShipping);
        }

        $this->_validate($accountData);
        $this->_customer->save();
        $this->_sendWelcomeEmail($customerData);

        $this->_eventManager->dispatch(
            'customer_service_customer_save_after',
            array('customer' => $this->_customer, 'customer_data' => $customerData)
        );
        return $this;
    }

    /**
     * Validate customer entity
     *
     * @param array $customerData
     * @return Mage_Customer_Service_Customer
     */
    protected function _validate($customerData)
    {
        /** @var $validatorFactory Magento_Validator_Config */
        $configFiles = Mage::getConfig()->getModuleConfigurationFiles('validation.xml');
        $validatorFactory = new Magento_Validator_Config($configFiles);
        $builder = $validatorFactory->getValidatorBuilder('customer', 'service_validator');

        $builder->addConfiguration('eav_validator', array(
            'method' => 'setAttributes',
            'arguments' => array($this->_getAttributesToValidate($this->_customer,
                $this->_deprecatedAttributes))
        ));
        $builder->addConfiguration('eav_validator', array(
            'method' => 'setData',
            'arguments' => array($customerData)
        ));
        $validator = $builder->createValidator();

        if (!$validator->isValid($this->_customer)) {
            $this->_processValidationErrors($validator->getMessages());
        }

        return $this;
    }

    /**
     * Gather error messages in one exception and throw it to presentation layer
     *
     * @param array $errorMessages
     * @throws Mage_Core_Exception
     */
    protected function _processValidationErrors($errorMessages)
    {
        $exception = new Mage_Core_Exception();
        foreach ($errorMessages as $errorMessage) {
            foreach ($errorMessage as $errorText) {
                if (!empty($errorText)) {
                    $exception->addMessage(Mage::getSingleton('Mage_Core_Model_Message')->error($errorText));
                }
            }
        }

        throw $exception;
    }

    /**
     * Get list of attributes to validate customer entity
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param array $deprecatedAttributes
     * @return array
     */
    protected function _getAttributesToValidate($customer, $deprecatedAttributes = null)
    {
        $attributesList = $this->_getCustomerAttributesList($customer);

        if (!empty($deprecatedAttributes)) {
            /** @var Mage_Eav_Model_Attribute $attribute */
            foreach ($attributesList as $attributeKey => $attribute) {
                if (in_array($attribute->getAttributeCode(), $deprecatedAttributes)) {
                    unset($attributesList[$attributeKey]);
                }
            }
        }

        return $attributesList;
    }

    /**
     * Get list of attributes of customer without loading its into customer model
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return array
     */
    protected function _getCustomerAttributesList($customer)
    {
        /** @var Mage_Eav_Model_Resource_Entity_Attribute_Collection $attrCollection */
        $attrCollection = $customer->getEntityType()->getAttributeCollection();
        return $attrCollection->getItems();
    }

    /**
     * Mark all customer addresses that were not passed in data for future removal
     *
     * @param array $customerData
     */
    protected function _markMissingAddressesForDelete($customerData)
    {
        $addresses = $this->_extractAddressesData($customerData);
        $modifiedAddressIds = array();
        foreach (array_keys($addresses) as $addressId) {
            $modifiedAddressIds[] = $addressId;
        }
        /** @var Mage_Customer_Model_Address $customerAddress */
        foreach ($this->_customer->getAddressesCollection() as $customerAddress) {
            if ($customerAddress->getId() && !in_array($customerAddress->getId(), $modifiedAddressIds)) {
                $customerAddress->setData('_deleted', true);
            }
        }
    }

    /**
     * Extract customer account data
     *
     * @param array $customerData
     * @return array
     */
    protected function _extractAccountData($customerData)
    {
        return isset($customerData['account']) ? $customerData['account'] : array();
    }

    /**
     * Extract customer addresses data
     *
     * @param array $customerData
     * @return array
     */
    protected function _extractAddressesData($customerData)
    {
        return isset($customerData['addresses']) ? $customerData['addresses'] : array();
    }

    /**
     * Set customer password
     *
     * @param array $customerAccountData
     */
    protected function _preparePasswordForSave($customerAccountData)
    {
        // TODO: 'force_confirmed' should be set in admin area only
        $this->_customer->setForceConfirmed(true);
        if ($this->_getAutogeneratePasswordFlag($customerAccountData)) {
            $this->_customer->setPassword($this->_customer->generatePassword());
        } elseif (isset($customerAccountData['password'])) {
            $this->_customer->setPassword($customerAccountData['password']);
        }
    }

    /**
     * Check if password should be generated automatically
     *
     * @param array $customerAccountData
     * @return bool
     */
    protected function _getAutogeneratePasswordFlag($customerAccountData)
    {
        return (isset($customerAccountData['autogenerate_password']) && $customerAccountData['autogenerate_password']);
    }

    /**
     * Retrieve send email flag
     *
     * @param array $customerAccountData
     * @return bool
     */
    protected function _getSendEmailFlag($customerAccountData)
    {
        return isset($customerAccountData['sendemail']) && $customerAccountData['sendemail'];
    }

    /**
     * Change customer password
     *
     * @param array $customerAccountData
     * @return Mage_Customer_Service_Customer
     */
    protected function _changePassword($customerAccountData)
    {
        $passwordSet = isset($customerAccountData['password']) && !empty($customerAccountData['password']);
        $autogeneratePassword = $this->_getAutogeneratePasswordFlag($customerAccountData);
        if ($passwordSet || $autogeneratePassword) {
            if ($autogeneratePassword) {
                $newPassword = $this->_customer->generatePassword();
            } else {
                $newPassword = $customerAccountData['password'];
            }
            $this->_customer->changePassword($newPassword);
            /**
             * TODO: If reminder sending is uncommented, the following exception occurs:
             * "Design config must have area and store."
             */
//            $this->_customer->sendPasswordReminderEmail();
        }
        return $this;
    }

    /**
     * Send welcome email to customer
     *
     * @param array $accountData
     * @return Mage_Customer_Service_Customer
     */
    protected function _sendWelcomeEmail($accountData)
    {
        if ($this->_customer->getWebsiteId()
            && ($this->_getSendEmailFlag($accountData) || $this->_getAutogeneratePasswordFlag($accountData))) {
            $isNewCustomer = $this->_customer->isObjectNew();
            $storeId = $this->_customer->getSendemailStoreId();
            if ($isNewCustomer) {
                $this->_customer->sendNewAccountEmail('registered', '', $storeId);
            } elseif ((!$this->_customer->getConfirmation())) {
                // Confirm not confirmed customer
                $this->_customer->sendNewAccountEmail('confirmed', '', $storeId);
            }
        }
        return $this;
    }

    /**
     * Load customer by its ID
     *
     * @param int|string $id
     * @throws Mage_Core_Exception
     * @return Mage_Customer_Model_Customer
     */
    protected function _loadCustomerById($id)
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer')->load($id);
        if (!$customer->getId()) {
            throw new Mage_Core_Exception($this->_translateHelper->__("The customer with the specified ID not found."));
        }
        return $customer;
    }

    /**
     * Prepare customer addresses for save
     *
     * @param array $addresses
     * @param int|null $defaultBillingIndex
     * @param int|null $defaultShippingIndex
     * @return Mage_Customer_Service_Customer
     */
    protected function _processAddresses($addresses, $defaultBillingIndex = null, $defaultShippingIndex = null)
    {
        $modifiedAddresses = array();
        if (!empty($addresses)) {
            foreach ($addresses as $addressIndex => $addressData) {
                $address = $this->_customer->getAddressItemById($addressIndex);
                if (!$address) {
                    $address = Mage::getModel('Mage_Customer_Model_Address');
                }
                $addressData['is_default_billing'] = ($defaultBillingIndex == $addressIndex);
                $addressData['is_default_shipping'] = ($defaultShippingIndex == $addressIndex);
                $address->addData($addressData);

                // TODO: Why do we need this setPostIndex() for?
                // Set post_index for detect default billing and shipping addresses
                $address->setPostIndex($addressIndex);
                if ($address->getId()) {
                    $modifiedAddresses[] = $address->getId();
                } else {
                    $this->_customer->addAddress($address);
                }
            }
        }
        return $this;
    }

    /**
     * Get Store Id
     * @return int
     */
    protected function _getStoreId()
    {
        return Mage::app()->getStore()->getId();
    }
}
