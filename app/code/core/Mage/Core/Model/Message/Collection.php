<?php

class Mage_Core_Model_Message_Collection
{
    protected $_messages = array();
    
    public function addMessage(Mage_Core_Model_Message_Abstract $message)
    {
        $this->_messages[] = $message;
    }
    
    public function getItems()
    {
        return $this->_messages;
    }
    
    public function clear()
    {
        $this->_messages = array();
    }
}