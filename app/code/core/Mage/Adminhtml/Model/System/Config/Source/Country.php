<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Adminhtml_Model_System_Config_Source_Country
{
    protected $_options;

    public function toOptionArray($isMultiselect=false)
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('Mage_Directory_Model_Resource_Country_Collection')
                ->loadData()
                ->toOptionArray(false);
        }

        $options = $this->_options;
        if(!$isMultiselect){
            array_unshift($options, array('value'=>'', 'label'=> Mage::helper('Mage_Adminhtml_Helper_Data')->__('--Please Select--')));
        }

        return $options;
    }
}
