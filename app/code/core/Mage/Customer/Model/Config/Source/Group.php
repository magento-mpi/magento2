<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Backend_Model_Config_Source_Customer_Group
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('Mage_Customer_Model_Resource_Group_Collection')
                ->setRealGroupsFilter()
                ->loadData()->toOptionArray();
            array_unshift($this->_options, array('value'=> '', 'label'=> Mage::helper('Mage_Backend_Helper_Data')->__('-- Please Select --')));
        }
        return $this->_options;
    }
}
