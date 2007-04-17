<?php

class Mage_Auth_Model_Session extends Varien_Data_Object 
{
    protected $_session = null;
    
    public function __construct()
    {
        $this->_session = new Zend_Session_Namespace('auth', Zend_Session_Namespace::SINGLE_INSTANCE);
    }
    
    public function setData($var, $value='', $isChanged=true)
    {
        $this->_session->$var = $value;
    }
    
    public function getData($var='', $index=false)
    {
        return $this->_session->$var;
    }

}