<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Customer_Model_Config_Source_Group implements Magento_Core_Model_Option_ArrayInterface
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('Mage_Customer_Model_Resource_Group_Collection')
                ->setRealGroupsFilter()
                ->loadData()->toOptionArray();
            array_unshift($this->_options, array('value'=> '', 'label'=> Mage::helper('Mage_Customer_Helper_Data')->__('-- Please Select --')));
        }
        return $this->_options;
    }
}
