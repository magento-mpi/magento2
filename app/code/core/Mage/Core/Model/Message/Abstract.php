<?php

abstract class Mage_Core_Model_Message_Abstract
{
    protected $_code;
    protected $_data;
    
    function __construct($code, $data=array())
    {
        $this->_code = $code;
        $this->_data = $data;
    }
    
    public function getCode()
    {
        return $this->_code;
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