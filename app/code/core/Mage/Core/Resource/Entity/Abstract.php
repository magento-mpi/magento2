<?php

abstract class Mage_Core_Resource_Entity_Abstract
{
    protected $_name = null;
    protected $_config = array();
    
    public function __construct($config)
    {
        $this->_config = $config;
    }
    
    public function getConfig($key='')
    {
        if (''===$key) {
        	return $this->_config;
        } elseif (isset($this->_config->$key)) {
        	return $this->_config->$key;
        } else {
            return false;
        }
    }    
}