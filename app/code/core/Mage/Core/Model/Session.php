<?php

class Mage_Core_Model_Session
{
    protected $_session;
    
    public function __construct()
    {
        $this->_session = new Zend_Session_Namespace('core', Zend_Session_Namespace::SINGLE_INSTANCE);
    }
    
    public function getMessages()
    {
        if (!$this->_session->messages) {
            $this->_session->messages = Mage::getModel('core_model', 'message_collection');
        }
        return $this->_session->messages;
    }
}