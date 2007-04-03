<?php

abstract class Mage_Core_Resource_Type_Abstract
{
    protected $_name = '';
    protected $_entityClass = 'Mage_Core_Resource_Entity_Abstract';

    public function getEntityClass()
    {
    	return $this->_entityClass;
    }
    
    public function setName($name)
    {
        $this->_name = $name;
    }
    
    public function getName()
    {
        return $this->_name;
    }
}