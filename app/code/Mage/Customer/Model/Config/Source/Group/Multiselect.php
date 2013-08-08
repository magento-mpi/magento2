<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Customer_Model_Config_Source_Group_Multiselect implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Customer groups options array
     *
     * @var null|array
     */
    protected $_options;

    /**
     * Retrieve customer groups as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('Mage_Customer_Model_Resource_Group_Collection')
                ->setRealGroupsFilter()
                ->loadData()->toOptionArray();
        }
        return $this->_options;
    }
}
