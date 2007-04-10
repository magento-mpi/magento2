<?php

abstract class Mage_Core_Model_Message_Abstract
{
    protected $_data = null;
    
    function __construct($data=array())
    {
        $this->_data = $data;
    }
    
    function getData()
    {
        return $this->_data;
    }
    
    function getType()
    {
        return 'message';
    }
}