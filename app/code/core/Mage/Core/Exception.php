<?php

class Mage_Core_Exception extends Zend_Exception 
{
    static protected $_messages = array();

    public function addMessage(Mage_Core_Model_Message_Abstract $message)
    {
        if (!isset(self::$_messages[$message->getType()])) {
            self::$_messages[$message->getType()] = array();
        }
        self::$_messages[$message->getType()][] = $message;
        return $this;
    }   
    
    public function getMessages($type='')
    {
        if ('' == $type) {
            $arrRes = array();
            foreach (self::$_messages as $messageType => $messages) {
                $arrRes = array_merge($arrRes, $messages);
            }
            return $arrRes;
        }
        return isset(self::$_messages[$type]) ? self::$_messages[$type] : array();
    }
}