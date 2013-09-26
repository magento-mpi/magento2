<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Customer_Model_Config_Source_Group implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var array
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

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = $this->_getCustomerGroupsCollection()->setRealGroupsFilter()->loadData()->toOptionArray();
            array_unshift($this->_options, array('value'=> '', 'label'=> __('-- Please Select --')));
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
