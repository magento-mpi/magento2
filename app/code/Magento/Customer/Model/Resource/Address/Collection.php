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
 * Customers collection
 *
 * @category    Magento
 * @package     Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Model_Resource_Address_Collection extends Magento_Eav_Model_Entity_Collection_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('Magento_Customer_Model_Address', 'Magento_Customer_Model_Resource_Address');
    }

    /**
     * Set customer filter
     *
     * @param Magento_Customer_Model_Customer|array $customer
     * @return Magento_Customer_Model_Resource_Address_Collection
     */
    public function setCustomerFilter($customer)
    {
        if (is_array($customer)) {
            $this->addAttributeToFilter('parent_id', array('in' => $customer));
        } elseif ($customer->getId()) {
            $this->addAttributeToFilter('parent_id', $customer->getId());
        } else {
            $this->addAttributeToFilter('parent_id', '-1');
        }
        return $this;
    }
}
