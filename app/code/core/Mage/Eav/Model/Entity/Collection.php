<?php

class Mage_Eav_Model_Entity_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    public function __construct()
    {
        $this->setConnection(Mage::getSingleton('core/resource')->getConnection('core_read'));
    }    
}