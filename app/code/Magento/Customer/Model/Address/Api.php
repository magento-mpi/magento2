<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer address api
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Model_Address_Api extends Magento_Customer_Model_Api_Resource
{
    protected $_mapAttributes = array(
        'customer_address_id' => 'entity_id'
    );

    public function __construct()
    {
        $this->_ignoredAttributeCodes[] = 'parent_id';
    }

    /**
     * Retrive customer addresses list
     *
     * @param int $customerId
     * @return array
     */
    public function items($customerId)
    {
        $customer = Mage::getModel('Magento_Customer_Model_Customer')
            ->load($customerId);
        /* @var $customer Magento_Customer_Model_Customer */

        if (!$customer->getId()) {
            $this->_fault('customer_not_exists');
        }

        $result = array();
        foreach ($customer->getAddresses() as $address) {
            $data = $address->toArray();
            $row  = array();

            foreach ($this->_mapAttributes as $attributeAlias => $attributeCode) {
                $row[$attributeAlias] = isset($data[$attributeCode]) ? $data[$attributeCode] : null;
            }

            foreach ($this->getAllowedAttributes($address) as $attributeCode => $attribute) {
                if (isset($data[$attributeCode])) {
                    $row[$attributeCode] = $data[$attributeCode];
                }
            }

            $row['is_default_billing'] = $customer->getDefaultBilling() == $address->getId();
            $row['is_default_shipping'] = $customer->getDefaultShipping() == $address->getId();

            $result[] = $row;

        }

        return $result;
    }

    /**
     * Retrieve address data
     *
     * @param int $addressId
     * @return array
     */
    public function info($addressId)
    {
        $address = Mage::getModel('Magento_Customer_Model_Address')
            ->load($addressId);

        if (!$address->getId()) {
            $this->_fault('not_exists');
        }

        $result = array();

        foreach ($this->_mapAttributes as $attributeAlias => $attributeCode) {
            $result[$attributeAlias] = $address->getData($attributeCode);
        }

        foreach ($this->getAllowedAttributes($address) as $attributeCode => $attribute) {
            $result[$attributeCode] = $address->getData($attributeCode);
        }


        if ($customer = $address->getCustomer()) {
            $result['is_default_billing']  = $customer->getDefaultBilling() == $address->getId();
            $result['is_default_shipping'] = $customer->getDefaultShipping() == $address->getId();
        }

        return $result;
    }

    /**
     * Delete address
     *
     * @param int $addressId
     * @return boolean
     */
    public function delete($addressId)
    {
        $address = Mage::getModel('Magento_Customer_Model_Address')
            ->load($addressId);

        if (!$address->getId()) {
            $this->_fault('not_exists');
        }

        try {
            $address->delete();
        } catch (Magento_Core_Exception $e) {
            $this->_fault('not_deleted', $e->getMessage());
        }

        return true;
    }
} // Class Magento_Customer_Model_Address_Api End
