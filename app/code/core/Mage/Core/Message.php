<?php

class Mage_Core_Message
{
    protected $_messages = array();
    protected $_messagesByType = array();
    
    function __construct()
    {
        
    }
    
    function addMessage(Mage_Core_Message_Abstract $message)
    {
        $this->_messages[] = $message;
        $this->_messagesByType[$message->getType()][] = $message;
    }
    
    function getMessages($type='')
    {
        if (''===$type) {
            return $this->_messages;
        } else {
            return $this->_messagesByType[$type];
        }
    }
}