<?php

class Varien_Filter_Sprintf implements Zend_Filter_Interface
{
    protected $_format = null;
    
    public function __construct($format)
    {
        $this->_format = $format;
    }
    
    public function filter($value)
    {
        return sprintf($this->_format, $value);
    }
}