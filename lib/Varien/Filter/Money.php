<?php

class Varien_Filter_Money implements Zend_Filter_Interface
{
    protected $_format = null;
    
    public function __construct($format)
    {
        $this->_format = $format;
    }
    
    public function filter($value)
    {
        return money_format($this->_format, $value);
    }
}