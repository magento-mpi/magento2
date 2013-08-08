<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Backend_Model_Config_Source_Store implements Magento_Core_Model_Option_ArrayInterface
{
    protected $_options;
    
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('Magento_Core_Model_Resource_Store_Collection')
                ->load()->toOptionArray();
        }
        return $this->_options;
    }
}
