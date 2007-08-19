<?php

class Mage_Core_Model_Session_Abstract_Varien extends Varien_Object
{
    public function start()
    {
    	if (isset($_SESSION)) {
    		return $this;
    	}
    	
        Varien_Profiler::start(__METHOD__.'/setOptions');
        session_save_path(Mage::getBaseDir('session'));
        Varien_Profiler::stop(__METHOD__.'/setOptions');
/*
        $sessionResource = Mage::getResourceSingleton('core/session');
        $sessionResource->setSaveHandler();
*/      
        Varien_Profiler::start(__METHOD__.'/start');
        session_start();
        Varien_Profiler::stop(__METHOD__.'/start');

        return $this;
    }

    public function addMessage(Mage_Core_Model_Message_Abstract $message)
    {
        $this->getMessages()->add($message);
        return $this;
    }
    
    public function init($namespace)
    {
    	if (!isset($_SESSION)) {
    		$this->start();
    	}
        if (!isset($_SESSION[$namespace])) {
        	$_SESSION[$namespace] = array();
        }
        $this->_data = &$_SESSION[$namespace];

        return $this;
    }
    
    public function addMessages($messages)
    {
        if (is_array($messages)) {
            foreach ($messages as $message) {
                $this->addMessage($message);
            }
        }
        return $this;
    }

    public function getMessages($clear=false)
    {
        if (!$this->getData('messages')) {
            $this->setData('messages', Mage::getModel('core/message_collection'));
        }
        
        if ($clear) {
            $messages = clone $this->getData('messages');
            $this->getData('messages')->clear();
            return $messages;
        }
        return $this->getData('messages');
    }

    public function getSessionId()
    {
        return session_id();
    }
    
    public function unsetAll()
    {
    	$this->unsetData();
    	return $this;
    }
}