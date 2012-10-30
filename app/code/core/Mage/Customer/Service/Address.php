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
 * Customer address service.
 */
class Mage_Customer_Service_Address extends Mage_Core_Service_ServiceAbstract
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
     * Get all customer addresses by customer id.
     *
     * @param int $customerId
     * @return Mage_Customer_Model_Address[] Array of addresses models; array keys - addresses' ids
     * @throws Mage_Core_Exception
     */
    public function getByCustomerId($customerId)
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer')->load($customerId);
        if (!$customer->getId()) {
            throw new Mage_Core_Exception($this->_translateHelper->__("The customer with the specified ID not found."));
        }
        return $customer->getAddresses();
    }

    /**
     * Create address entity
     *
     * @param array $addressData
     * @param int|string $customerId
     * @return Mage_Customer_Model_Address
     */
    public function create(array $addressData, $customerId)
    {
        $this->_checkCustomerExists($customerId);

        /** @var Mage_Customer_Model_Address $address */
        $address = $this->_config->getModelInstance('Mage_Customer_Model_Address');
        $this->_setDataUsingMethods($address, $addressData);
        $address->setCustomerId($customerId);

        $address->save();
        return $address;
    }

    /**
     * Check that $customerId refers to existent customer.
     *
     * @param int|string $customerId
     * @throws Mage_Core_Exception when customer is not found
     */
    protected function _checkCustomerExists($customerId)
    {
        /** @var Mage_Customer_Model_Resource_Customer $resource */
        $resource = Mage::getResourceModel('Mage_Customer_Model_Resource_Customer');
        if (!$resource->checkCustomerId($customerId)) {
            throw new Mage_Core_Exception($this->_translateHelper->__("The customer with the specified ID not found."));
        }
    }

    /**
     * Update address entity
     *
     * @param int|string $addressId
     * @param array $addressData
     * @return Mage_Customer_Model_Address
     */
    public function update($addressId, array $addressData)
    {
        $address = $this->_getById($addressId);
        $this->_setDataUsingMethods($address, $addressData);

        $address->save();
        return $address;
    }

    /**
     * Get address by id.
     *
     * @param int $addressId
     * @return Mage_Customer_Model_Address
     * @throws Mage_Core_Exception if address not found
     */
    protected function _getById($addressId)
    {
        /** @var Mage_Customer_Model_Address $address */
        $address = Mage::getModel('Mage_Customer_Model_Address')->load($addressId);
        if (!$address->getId()) {
            throw new Mage_Core_Exception($this->_translateHelper->__("The address with the specified ID not found."));
        }
        return $address;
    }
}
