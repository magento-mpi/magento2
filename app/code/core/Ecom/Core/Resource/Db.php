<?php

abstract class Ecom_Core_Resource_Db extends Ecom_Core_Resource_Abstract 
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_defaultEntityClass = 'Ecom_Core_Resource_Entity_Table';
    }
}