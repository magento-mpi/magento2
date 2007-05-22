<?php

abstract class Mage_Core_Model_Resource_Type_Db extends Mage_Core_Model_Resource_Type_Abstract 
{
    public function __construct()
    {
        $this->_entityClass = 'Mage_Core_Model_Resource_Entity_Table';
    }
}