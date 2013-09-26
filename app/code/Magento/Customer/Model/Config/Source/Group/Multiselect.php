<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Customer_Model_Config_Source_Group_Multiselect implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Customer groups options array
     *
     * @var null|array
     */
    protected $_options;

    /**
     * @var Magento_Customer_Model_Resource_Group_CollectionFactory
     */
    protected $_groupsFactory;

    /**
     * @param Magento_Customer_Model_Resource_Group_CollectionFactory $groupsFactory
     */
    public function __construct(Magento_Customer_Model_Resource_Group_CollectionFactory $groupsFactory)
    {
        $this->_groupsFactory = $groupsFactory;
    }

    /**
     * Retrieve customer groups as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = $this->_getCustomerGroupsCollection()->setRealGroupsFilter()->loadData()->toOptionArray();
        }
        return $this->_options;
    }

    /**
     * @return Magento_Customer_Model_Resource_Group_Collection
     */
    protected function _getCustomerGroupsCollection()
    {
        return $this->_groupsFactory->create();
    }
}
