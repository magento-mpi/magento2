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
 * Customer group attribute source
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Model_Customer_Attribute_Source_Group extends Magento_Eav_Model_Entity_Attribute_Source_Table
{
    /**
     * @var Magento_Customer_Model_Resource_Group_CollectionFactory
     */
    protected $_groupsFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Customer_Model_Resource_Group_CollectionFactory $groupsFactory
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Customer_Model_Resource_Group_CollectionFactory $groupsFactory
    ) {
        $this->_groupsFactory = $groupsFactory;
        parent::__construct($coreData);
    }

    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = $this->_getCustomerGroupsCollection()->setRealGroupsFilter()->load()->toOptionArray();
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
