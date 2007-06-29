<?php
abstract class Mage_Core_Controller_Varien_Router_Abstract
{
    protected $_front;
    
    public function setFront($front)
    {
        $this->_front = $front;
        return $this;
    }
    
    public function getFront()
    {
        return $this->_front;
    }
    
    abstract public function match(Zend_Controller_Request_Http $request);
}