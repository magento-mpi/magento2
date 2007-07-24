<?php

class Mage_Core_Model_Mysql4_Config_Data extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('core/config_data', 'config_id');
    }
    
    /**
     * Perform actions after object save
     *
     * @param Varien_Object $object
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        return $this;
    }
    
}