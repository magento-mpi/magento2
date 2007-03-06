<?php

abstract class Mage_Core_Resource_Db extends Mage_Core_Resource_Abstract 
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_defaultEntityClass = 'Mage_Core_Resource_Entity_Table';
    }
}