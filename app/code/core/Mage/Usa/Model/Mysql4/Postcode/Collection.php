<?php
class Mage_Usa_Model_Mysql4_Postcode_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
    	$this->_init('usa/postcode');
    }

    public function setRegionFilter($regionId)
    {
        $this->getSelect()->where("main_table.region_id = '{$regionId}'");
    }
}