<?php

class Mage_Core_Model_Message_Collection
{
    protected $_messages = array();
    
    public function add(Mage_Core_Model_Message_Abstract $message)
    {
        return $this->addMessage($message);
    }
    
    public function addMessage(Mage_Core_Model_Message_Abstract $message)
    {
        if (!isset($this->_messages[$message->getType()])) {
            $this->_messages[$message->getType()] = array();
        }
        $this->_messages[$message->getType()][] = $message;
        return $this;
    }

    public function clear()
    {
        $this->_messages = array();
        return $this;
    }
    
    public function getItems()
    {
        $arrRes = array();
        foreach ($this->_messages as $messageType => $messages) {
            $arrRes = array_merge($arrRes, $messages);
        }
        
        return $arrRes;
    }
    
    public function getItemsByType($type)
    {
        return isset($this->_messages[$type]) ? $this->_messages[$type] : array();
    }
    
    public function getErrors()
    {
        return $this->getItemsByType(Mage_Core_Model_Message::ERROR);
    }
    
    public function toHtml()
    {
        $out = '';
        $arrItems = $this->getItems();
        foreach ($arrItems as $item) {
            $out.= $item->toHtml();
        }
        
        return $out;
    }

    public function count()
    {
        return count($this->_messages);
    }
}