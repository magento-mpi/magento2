<?php

abstract class Mage_Core_Model_Message_Abstract
{
    protected $_type;
    protected $_code;
    protected $_class;
    protected $_method;
    
    public function __construct($type, $code='')
    {
        $this->_type = $type;
        $this->_code = $code;
    }

    public function getCode()
    {
        return $this->_code;
    }
    
    public function getType()
    {
        return $this->_type;
    }

    public function setClass($class)
    {
        $this->_class = $class;
    }
    
    public function setMethod($method)
    {
        $this->_method = $method;
    }

    public function __toHtml()
    {
        $out = '<div class="'.$this->getType().'">'.$this->getCode().'</div>';
        return $out;
    }
}