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
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('Magento_Customer_Model_Resource_Group_Collection')
                ->setRealGroupsFilter()
                ->loadData()->toOptionArray();
            array_unshift($this->_options, array('value'=> '', 'label'=> __('-- Please Select --')));
        }
        return $this->_options;
    }
}
