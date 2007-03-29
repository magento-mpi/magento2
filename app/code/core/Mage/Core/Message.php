<?php

class Mage_Core_Message
{
    protected $_messages = array();
    protected $_messagesByType = array();
    
    function __construct()
    {
    }
    
/*    function addMessage(Mage_Core_Message_Abstract $message)
    {
        $this->_messages[] = $message;
        $this->_messagesByType[$message->getType()][] = $message;
        
        $this->_sessionNamespace->messages = $this->_messages;
    }*/
    
    function addMessage($message, $messageType = 'notice')
    {
        if (is_string($message)) {
            
        }
        switch (strtolower($messageType)) {
        	case 'error':
        		$message = new Mage_Core_Message_Error($message);
        		break;
            case 'warning':
                $message = new Mage_Core_Message_Warning($message);
                break;
        	default:
        	    $message = new Mage_Core_Message_Notice($message);
        		break;
        }
        
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