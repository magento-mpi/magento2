<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Backend_Model_Config_Source_Store
{
    protected $_options;
    
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('Mage_Core_Model_Resource_Store_Collection')
                ->load()->toOptionArray();
        }
        return $this->_options;
    }
}
