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
 * Customer group collection
 *
 * @category    Magento
 * @package     Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Model_Resource_Group_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('Magento_Customer_Model_Group', 'Magento_Customer_Model_Resource_Group');
    }

    /**
     * Set ignore ID filter
     *
     * @param array $indexes
     * @return Magento_Customer_Model_Resource_Group_Collection
     */
    public function setIgnoreIdFilter($indexes)
    {
        if (count($indexes)) {
            $this->addFieldToFilter('main_table.customer_group_id', array('nin' => $indexes));
        }
        return $this;
    }

    /**
     * Set real groups filter
     *
     * @return Magento_Customer_Model_Resource_Group_Collection
     */
    public function setRealGroupsFilter()
    {
        return $this->addFieldToFilter('customer_group_id', array('gt' => 0));
    }

    /**
     * Add tax class
     *
     * @return Magento_Customer_Model_Resource_Group_Collection
     */
    public function addTaxClass()
    {
        $this->getSelect()->joinLeft(
            array('tax_class_table' => $this->getTable('tax_class')),
            "main_table.tax_class_id = tax_class_table.class_id");
        return $this;
    }

    /**
     * Retreive option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return parent::_toOptionArray('customer_group_id', 'customer_group_code');
    }

    /**
     * Retreive option hash
     *
     * @return array
     */
    public function toOptionHash()
    {
        return parent::_toOptionHash('customer_group_id', 'customer_group_code');
    }
}
