<?php

class Mage_Core_Model_Mysql4_Config_Value_Collection extends Mage_Core_Model_Resource_Collection_Abstract 
{
    public function _construct()
    {
        $this->setModel('core/config_value');
    }
    
    public function setSectionFilter($sectionId)
    {
        $this->getSelect()->where('main_table.section_id=?', $sectionId);
        return $this;
    }
}