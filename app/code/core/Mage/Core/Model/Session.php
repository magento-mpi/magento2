<?php

class Mage_Core_Model_Session
{
    protected $_session;
    
    public function __construct($namespace)
    {
        if(empty($namespace) || !is_string($namespace))
        {
            $namespace = 'core';
        }

        $this->_session = new Zend_Session_Namespace($namespace, Zend_Session_Namespace::SINGLE_INSTANCE);
    }
    
    public function addMessage(Mage_Core_Model_Message_Abstract $message)
    {
        $this->getMessages()->add($message);
    }
    
    public function addMessages($messages)
    {
        if (is_array($messages)) {
            foreach ($messages as $message) {
                $this->addMessage($message);
            }
        }
    }

    public function getMessages($clear=false)
    {
        if (!$this->_session->messages) {
            $this->_session->messages = Mage::getModel('core_model', 'message_collection');
        }
        
        if ($clear) {
            $messages = clone $this->_session->messages;
            $this->_session->messages->clear();
            return $messages;
        }
        return $this->_session->messages;
    }
}