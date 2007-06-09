<?php

class Mage_Core_Model_Session_Abstract extends Varien_Object
{
    protected $_session;
    
    public function init($namespace)
    {
        $this->_session = new Zend_Session_Namespace($namespace, Zend_Session_Namespace::SINGLE_INSTANCE);
    }
    
    public function setData($key, $value='', $isChanged=true)
    {
        if (!$this->_session->data) {
            $this->_session->data = new Varien_Object();
        }
        $this->_session->data->setData($key, $value, $isChanged);
        return $this;
    }

    public function getData($var=null, $clear=false)
    {
        if (!$this->_session->data) {
            $this->_session->data = new Varien_Object();
        }

        $data = $this->_session->data->getData($var);
        
        if ($clear) {
            unset($this->_session->data->$var);
        }

        return $data;
    }

    public function unsetAll()
    {
        $this->_session->unsetAll();
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
        return $this;
    }

    public function getMessages($clear=false)
    {
        if (!$this->_session->messages) {
            $this->_session->messages = Mage::getModel('core/message_collection');
        }
        
        if ($clear) {
            $messages = clone $this->_session->messages;
            $this->_session->messages->clear();
            return $messages;
        }
        return $this->_session->messages;
    }

    public function getSessionId()
    {
        return Zend_Session::getId();
    }
}