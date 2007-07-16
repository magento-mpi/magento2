<?php

class Mage_Core_Model_Session_Varien extends Varien_Object
{
    protected $_namespace;

    public function init($namespace)
    {
        $this->_namespace = $namespace;
        $this->setData(array());
        return $this;
    }
    
    public function start()
    {
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
    
    public function setData($key, $value='', $isChanged=true)
    {
        if (is_array($key)) {
            $_SESSION[$this->_namespace] = $key;
        } elseif (is_string($key)) {
            $_SESSION[$this->_namespace][$key] = $value;
        }
        return $this;
    }
    
    public function addData($data)
    {
        foreach ($data as $k=>$v) {
            $this->setData($k, $v);
        }
        return $this;
    }

    public function getData($var=null, $clear=false)
    {
        if (is_null($var)) {
            return $_SESSION[$this->_namespace];
        } elseif (isset($_SESSION[$this->_namespace][$var])) {
            $result = isset($_SESSION[$this->_namespace][$var]);
            if ($clear) {
                unset($_SESSION[$this->_namespace][$var]);
            }
            return $result;
        }
        return false;
    }

    public function unsetAll()
    {
        unset($_SESSION[$this->_namespace]);
        return $this;
    }
    
    public function addMessage(Mage_Core_Model_Message_Abstract $message)
    {
        $this->getMessages()->add($message);
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
}