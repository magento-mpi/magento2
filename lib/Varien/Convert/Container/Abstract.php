<?php

abstract class Varien_Convert_Container_Abstract implements Varien_Convert_Container_Interface
{
    protected $_params;
    protected $_vars;
    
    public function getParam($key=null, $default=null)
    {
        if (is_null($key)) {
            return $this->_params;
        }
        if (!isset($this->_params[$key])) {
            return $default;
        }
        return $this->_params[$key];
    }
    
    public function setParam($key, $value=null)
    {
        if (is_array($key) && is_null($value)) {
            $this->_param = $key;
        } else {
            $this->_param[$key] = $value;
        }
        return $this;
    }
}