<?php
/**
 * {license}
 *
 * @category    Mage
 * @package     Mage_Customer
 */

/**
 * API2 class for customer address
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Api2_Customer_Address extends Mage_Api2_Model_Resource
{
    /**
     * Resource specific method to retrieve attributes' codes. May be overriden in child.
     *
     * @return array
     */
    protected function _getResourceAttributes()
    {
        return $this->getEavAttributes(Mage_Api2_Model_Auth_User_Admin::USER_TYPE != $this->getUserType());
    }

    /**
     * Get customer address resource validator instance
     *
     * @return Mage_Customer_Model_Api2_Customer_Address_Validator
     */
    protected function _getValidator()
    {
        return Mage::getModel('Mage_Customer_Model_Api2_Customer_Address_Validator', array('resource' => $this));
    }

    /**
     * Is specified address a default billing address?
     *
     * @param Mage_Customer_Model_Address $address
     * @return bool
     */
    protected function _isDefaultBillingAddress(Mage_Customer_Model_Address $address)
    {
        return $address->getCustomer()->getDefaultBilling() == $address->getId();
    }

    /**
     * Is specified address a default shipping address?
     *
     * @param Mage_Customer_Model_Address $address
     * @return bool
     */
    protected function _isDefaultShippingAddress(Mage_Customer_Model_Address $address)
    {
        return $address->getCustomer()->getDefaultShipping() == $address->getId();
    }

    /**
     * Get region id by name or code
     * If id is not found then return passed $region
     *
     * @param string $region
     * @return int|string
     */
    protected function _getRegionIdByNameOrCode($region)
    {
        $id = Mage::getResourceModel('Mage_Directory_Model_Resource_Region_Collection')
            ->addFieldToFilter(array('default_name', 'code'), array($region, $region))
            ->getFirstItem()
            ->getId();
        return $id ? $id : $region;
    }

    /**
     * Load customer address by id
     *
     * @param int $id
     * @return Mage_Customer_Model_Address
     */
    protected function _loadCustomerAddressById($id)
    {
        /* @var $address Mage_Customer_Model_Address */
        $address = Mage::getModel('Mage_Customer_Model_Address')->load($id);

        if (!$address->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        $address->addData($this->_getDefaultAddressesInfo($address));

        return $address;
    }

    /**
     * Load customer by id
     *
     * @param int $id
     * @throws Mage_Api2_Exception
     * @return Mage_Customer_Model_Customer
     */
    protected function _loadCustomerById($id)
    {
        /* @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer')->load($id);
        if (!$customer->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $customer;
    }
}
